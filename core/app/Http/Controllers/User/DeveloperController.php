<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ApiKey;

class DeveloperController extends Controller
{
    public function apiDocs()
    {
        $pageTitle  = "API Documentation";
        $user      = auth()->user();
        $errorCodes = [
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            406 => 'Not Acceptable',
            422 => 'Unprocessable Entity',
            424 => 'Failed Dependency',
            500 => 'Internal Server Error',
        ];
        return view('Template::user.developer.docs', compact('pageTitle', 'user', 'errorCodes'));
    }

    public function regenerateApiKey()
    {
        $user = auth()->user();
        ApiKey::where('user_id', $user->id)->where('status', Status::ENABLE)->update(['status' => Status::DISABLE]);

        $apiKey           = new ApiKey();
        $apiKey->key      = getTrx(40) . $user->id;
        $apiKey->user_id  = $user->id;
        $apiKey->save();

        $notify[] = ['success', "API key regenerated successfully"];
        return back()->withNotify($notify);
    }
}
