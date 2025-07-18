<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;

class BatchController extends Controller
{
    public function smsBatch()
    {
        $pageTitle  = 'Manage  Batch';
        $batches    = Batch::with(['sms', 'user'])->searchable(['batch_id', 'user:username'])->dateFilter()->orderBy('id', 'DESC')->paginate(getPaginate());
        return view('admin.batch.sms_batch', compact('pageTitle', 'batches'));
    }
}
