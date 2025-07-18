<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Template';
        $templates = Template::searchable(['name','user:username'])->filter(['status', 'user:username'])->with('user')->orderBy('id', 'DESC')->paginate(getPaginate());
        $columns   = Template::getColumNames();
        return view('admin.template.index', compact('pageTitle', 'templates', 'columns'));
    }

    public function exportTemplate(Request $request)
    {
        $request->validate([
            'columns'     => 'required|array',
            'export_item' => 'required|integer',
            'order_by'    => 'required|in:ASC,DESC',
        ]);

        return exportData('template', $request->columns, $request->export_item, $request->order_by);
    }
}
