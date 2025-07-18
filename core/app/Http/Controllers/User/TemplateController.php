<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{

    public function index()
    {
        $pageTitle = 'Manage Template';
        $templates = Template::belongsToUser()->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('Template::user.template.index', compact('pageTitle', 'templates'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'name'    => 'required|string',
            'message' => 'required|string',
        ]);

        $templates = Template::belongsToUser()->where('name', $request->name)->where('id', '!=', $id)->exists();
        if ($templates) {
            $notify[] = ['error', 'Template name already exists'];
            return back()->withNotify($notify);
        }

        if ($id) {
            $template         = Template::belongsToUser()->findOrFail($id);
            $message          = "Template updated successfully";
        } else {
            $template = new Template();
            $message  = "Template added successfully";
        }

        $template->user_id = auth()->id();
        $template->name    = $request->name;
        $template->message = $request->message;
        $template->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        Template::belongsToUser()->findOrFail($id);
        return Template::changeStatus($id);
    }
}
