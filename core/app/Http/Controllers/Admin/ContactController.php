<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Contacts';
        $contacts  = Contact::with('user')->searchable(['mobile', 'city', 'state', 'zip', 'country', 'email'])->filter(['status', 'user:username'])->orderBy('id', 'DESC')->paginate(getPaginate());
        $columns   = Contact::getColumNames();
        return view('admin.contact.index', compact('pageTitle', 'contacts', 'columns'));
    }

    public function exportContact(Request $request)
    {
        $request->validate([
            'columns'     => 'required|array',
            'export_item' => 'required|integer',
            'order_by'    => 'required|in:ASC,DESC',
        ]);

        return exportData('contact', $request->columns, $request->export_item, $request->order_by);
    }
}
