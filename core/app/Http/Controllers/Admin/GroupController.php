<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups    = Group::searchable(['name'])->filter(['status', 'user:username'])->withCount('contact as total_contact')->paginate(getPaginate());
        $pageTitle = "Manage Group";
        $columns   = Group::getColumNames();
        return view('admin.group.index', compact('pageTitle', 'groups', 'columns'));
    }

    public function viewGroupContact($id)
    {
        $group     = Group::where('id', $id)->firstOrFail();
        $pageTitle = 'View Group: ' . $group->name;

        $contacts  = Contact::whereHas('groupContact', function ($query) use ($group) {
            $query->where('group_id', $group->id);
        })->with('user')->searchable(['mobile', 'city', 'state', 'zip', 'country', 'email'])->filter(['status', 'user:username'])->orderBy('id', 'DESC')->paginate(getPaginate());

        $columns   = Contact::getColumNames();
        return view('admin.contact.index', compact('pageTitle', 'contacts', 'columns', 'group'));
    }

    public function exportGroupContacts(Request $request)
    {
        $request->validate([
            'columns'     => 'required|array',
            'export_item' => 'required|integer',
            'order_by'    => 'required|in:ASC,DESC',
        ]);

        return exportData('group', $request->columns, $request->export_item, $request->order_by);
    }

    public function exportGroupContact(Request $request, $id){

        $request->validate([
            'columns'     => 'required|array',
            'export_item' => 'required|integer',
            'order_by'    => 'required|in:ASC,DESC',
        ]);

        $group = Group::findOrFail($id);

        $contacts = Contact::whereHas('groupContact', function($query) use($group){
            $query->where('group_id', $group->id);
        })->orderBy('id', $request->order_by)->limit($request->export_item)->select($request->columns)->get();


        return exportData('contact', $request->columns, $request->export_item, $request->order_by, $contacts);
    }

    public function exportGroup(Request $request)
    {
        $request->validate([
            'columns'     => 'required|array',
            'export_item' => 'required|integer',
            'order_by'    => 'required|in:ASC,DESC',
        ]);

        return exportData('group', $request->columns, $request->export_item, $request->order_by);
    }
}
