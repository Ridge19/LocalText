<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Batch;

class BatchController extends Controller
{
    public function smsBatch()
    {
        $pageTitle = 'Manage  Batch';
        $batches     = Batch::belongsToUser()->with('sms')->orderBy('id', 'DESC')->paginate(getPaginate());

        return view('Template::user.batch.sms_batch', compact('pageTitle', 'batches'));
    }
}
