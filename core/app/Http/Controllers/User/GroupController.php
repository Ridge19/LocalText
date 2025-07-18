<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Group;
use App\Models\GroupContact;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::with('plan')
            ->belongsToUser()
            ->searchable(['name'])
            ->filter(['status'])
            ->orderBy('id', 'DESC')
            ->withCount('contact as total_contact')
            ->paginate(getPaginate());

        $pageTitle = "Manage Group";
        return view('Template::user.group.index', compact('pageTitle', 'groups'));
    }

    public function saveGroup(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $groupExists = Group::belongsToUser()->whereNot('id', $id)->where('name', $request->name)->exists();

        if ($groupExists) {
            $notify[] = ['error', 'Group already exists'];
            return back()->withNotify($notify);
        }

        $user = auth()->user();

        if (!$id) {
            if (!$user->hasLimit('available_group_limit')) {
                $notify[] = ['error', 'You have reached the maximum number of group limit'];
                return back()->withNotify($notify);
            }
        }

        if ($id) {
            $group   = Group::belongsToUser()->findOrFail($id);
            $message = "Group updated successfully";
        } else {
            $group   = new Group();
            $message = "Group added successfully";
        }

        $group->user_id = auth()->id();
        $group->name    = $request->name;
        $group->save();

        $user->subtractLimitCounter('available_group_limit');

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function viewGroupContact($id)
    {
        $group     = Group::belongsToUser()->where('id', $id)->firstOrFail();
        $pageTitle = 'View Group: ' . $group->name;
        $contacts  = GroupContact::where('group_id', $id)->orderBy('id', 'DESC')->with('contact', 'contact')->paginate(getPaginate());
        return view('Template::user.group.view_contact', compact('pageTitle', 'group', 'contacts'));
    }

    public function contactSaveToGroup(Request $request, $groupId)
    {
        $request->validate([
            'contacts'   => 'required|array',
            'contacts.*' => 'required|integer|exists:contacts,id',
        ]);

        $group = Group::belongsToUser()->where('id', $groupId)->firstOrFail();

        if (!$group->status) {
            $notify[] = ['error', 'Currently Group is inactive'];
            return back()->withNotify($notify);
        }

        $contactId = [];

        $validContacts = Contact::belongsToUser()
            ->whereIn('id', $request->contacts)
            ->pluck('id')
            ->toArray();

        foreach ($validContacts as $contact) {
            $groupContact = GroupContact::where('group_id', $request->group_id)->where('contact_id', $contact)->exists();
            if (!$groupContact) {
                $contactId[] = $contact;
            }
        }
        $group->contact()->attach($contactId, [
            'user_id'    => auth()->id(),
            'created_at' => now()
        ]);

        $notify[] = ['success', "Contact added successfully"];
        return back()->withNotify($notify);
    }

    public function deleteContactFromGroup($id)
    {
        $groupContact = GroupContact::where('user_id', auth()->id())->findOrFail($id);

        $groupContact->delete();
        $notify[] = ['success', "Contact removed successfully"];
        return back()->withNotify($notify);
    }

    public function importContactToGroup(Request $request, $groupId)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', new FileTypeValidate(['csv', 'xlsx', 'txt'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->all(),
            ]);
        }

        $group = Group::belongsToUser()->active()->where('id', $groupId)->first();

        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => "Group not found",
            ]);
        }

        $columnNames = ['firstname', 'lastname', 'email', 'dial_code', 'mobile', 'country', 'city', 'state', 'zip'];
        $contactId   = [];
        $collectionDialCode  = [];
        $collectionMobile  = [];

        try {
            $fileReadData = importFileReader($request->file, $columnNames, $columnNames);
            if (count($fileReadData->allData)) {
                foreach ($fileReadData->allData as $item) {
                    $collectionDialCode[] = @$item[3];
                    $collectionMobile[] = @$item[4];
                }
            }

            $contactId = Contact::belongsToUser()
                ->where('status', Status::ENABLE)
                ->whereIn('dial_code', $collectionDialCode)
                ->whereIn('mobile', $collectionMobile)
                ->select('id')
                ->pluck('id')
                ->toArray();

            $alreadyExistContactId = GroupContact::where('group_id', $group->id)
                ->pluck('contact_id')
                ->toArray();

            $newContactId = array_diff($contactId, $alreadyExistContactId);

            if (count($newContactId) > 0) {
                $group->contact()->attach($newContactId, [
                    'user_id'    => auth()->id(),
                    'created_at' => Carbon::now(),
                ]);
            }

            $notify['success'] = true;
            $notify['message'] = count($newContactId) . " contacts added to group";
        } catch (Exception $ex) {
            $notify['success'] = false;
            $notify['message'] = $ex->getMessage();
        }
        return response()->json($notify);
    }

    public function status($id)
    {
        Group::belongsToUser()->findOrFail($id);
        return Group::changeStatus($id);
    }
}
