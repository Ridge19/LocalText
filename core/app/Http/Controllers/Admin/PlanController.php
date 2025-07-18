<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function list()
    {
        $pageTitle = "All Plans";
        $plans = Plan::orderby('id', 'DESC')->paginate(getPaginate());
        return view('admin.plan.index', compact('pageTitle', 'plans'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name'            => 'required|string',
            'title'           => 'required|string',
            'price'           => 'required|numeric|gte:0',
            'image'           => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'device_limit'    => 'required|integer|gte:-1',
            'sms_limit'       => 'required|integer|gte:-1',
            'daily_sms_limit' => 'required|integer|gte:-1|lte:sms_limit',
            'contact_limit'   => 'required|integer|gte:-1',
            'group_limit'     => 'required|integer|gte:-1',
            'recurring_type'  => ['required', Rule::in([Status::MONTHLY_PLAN, Status::YEARLY_PLAN])],
        ]);

        if ($id) {
            $plan  = Plan::findOrFail($id);
            $message = "Plan updated successfully";
        } else {
            $plan  = new Plan();
            $message = "Plan added successfully";
        }

        $plan->name            = $request->name;
        $plan->title           = $request->title;
        $plan->price           = $request->price;
        $plan->device_limit    = $request->device_limit;
        $plan->sms_limit       = $request->sms_limit;
        $plan->daily_sms_limit = $request->daily_sms_limit;
        $plan->contact_limit   = $request->contact_limit;
        $plan->group_limit     = $request->group_limit;
        $plan->recurring_type  = $request->recurring_type;
        $plan->api_available   = $request->api_available  ? Status::YES : Status::NO;
        $plan->scheduled_sms   = $request->scheduled_sms  ? Status::YES : Status::NO;
        $plan->campaign        = $request->campaign       ? Status::YES : Status::NO;

        if ($request->hasFile('image')) {
            try {
                $plan->image = fileUploader($request->image, getFilePath('plan'), getFileSize('plan'), $plan->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $plan->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Plan::changeStatus($id);
    }
}
