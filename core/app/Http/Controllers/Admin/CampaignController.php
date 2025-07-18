<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;

class CampaignController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Campaigns';
        $campaigns = Campaign::with('user')->searchable(['title', 'user:username'])->filter(['status', 'user:username'])->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.campaign.index', compact('pageTitle', 'campaigns'));
    }
}
