<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Rules\FileTypeValidate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        $pageTitle  = 'Manage Contacts';
        $contacts   = Contact::belongsToUser()->searchable(['mobile', 'dial_code'])->filter(['status', 'country'])->orderBy('id', 'DESC')->paginate(getPaginate());
        $columns    = Contact::getColumNames();
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.contact.index', compact('pageTitle', 'contacts', 'columns', 'mobileCode', 'countries'));
    }

    public function save(Request $request, $id = 0)
    {
        $countryData  = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country'      => 'in:' . $countries,
            'mobile_code'    => 'required|in:' . $mobileCodes,
            'mobile'       => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
        ]);

        $user = auth()->user();
        if (!$id) {
            if (!$user->hasLimit('available_contact_limit')) {
                $notify[] = ['error', 'You have reached the maximum number of contact limit'];
                return back()->withNotify($notify);
            }
        }

        $contactExists = Contact::belongsToUser()
            ->where('id', '!=', $id)
            ->where('dial_code', $request->mobile_code)
            ->where('mobile', $request->mobile)
            ->exists();

        if ($contactExists) {
            $notify[] = ['error', 'Contact already exists'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $contact = Contact::belongsToUser()->findOrFail($id);
            $message = 'Contact updated successfully';
        } else {
            $message = 'New contact added successfully';
            $contact = new Contact();
        }

        $contact->user_id     = $user->id;
        $contact->firstname   = $request->firstname;
        $contact->lastname    = $request->lastname;
        $contact->email       = $request->email;
        $contact->dial_code   = $request->mobile_code;
        $contact->mobile      = $request->mobile;
        $contact->country     = $request->country;
        $contact->city        = $request->city;
        $contact->state       = $request->state;
        $contact->zip         = $request->zip;
        $contact->save();

        $user->subtractLimitCounter('available_contact_limit');

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        Contact::belongsToUser()->findOrFail($id);
        return Contact::changeStatus($id);
    }

    public function importContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'max:3072', new FileTypeValidate(['csv', 'xlsx', 'txt'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validation Error",
                'errors'  => $validator->errors()->all(),
            ]);
        }

        $columnNames = ['firstname', 'lastname', 'email', 'dial_code', 'mobile', 'country', 'city', 'state', 'zip'];
        $notify      = [];
        try {
            $import = importFileReader($request->file, $columnNames, $columnNames);
            $notify = $import->notifyMessage();
        } catch (Exception $ex) {
            $notify['success'] = false;
            $notify['message'] = $ex->getMessage();
        }
        return response()->json($notify);
    }

    public function exportContact(Request $request)
    {
        $request->validate([
            'columns'     => 'required|array',
            'export_item' => 'required|integer',
            'order_by'    => 'required|in:ASC,DESC',
        ]);

        $contact                = new Contact();
        $contact->exportColumns = $request->columns;
        $contact->fileName      = 'contact.csv';
        $contact->exportItem    = $request->export_item;
        $contact->orderBy       = $request->order_by;

        return $contact->export();
    }

    public function contactSearch()
    {
        $query = Contact::belongsToUser()->active()->searchable(['mobile']);

        if (request()->group_id) {
            $query->whereDoesntHave('groupContact', function ($q) {
                $q->where('group_id', request()->group_id);
            });
        }
        $contacts = $query->paginate(getPaginate());

        return response()->json([
            'success'  => true,
            'contacts' => $contacts,
            'more'     => $contacts->hasMorePages(),
        ]);
    }
}
