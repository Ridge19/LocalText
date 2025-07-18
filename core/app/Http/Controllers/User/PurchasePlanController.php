<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PurchasePlan;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PurchasePlanController extends Controller
{
    public function purchasePlan(Request $request)
    {
        $request->validate([
            'plan_id'       => 'required|integer',
            'payment_type' => ['required', Rule::in([Status::WALLET_PAYMENT, Status::GATEWAY_PAYMENT])],
        ]);

        $user = auth()->user();
        $plan = Plan::active()->find($request->plan_id);

        if (!$plan) {
            $notify[] = ['error', 'Sorry! this plan not found'];
            return back()->withNotify($notify);
        }

        if ($user->plan_id == $plan->id) {
            $notify[] = ['error', 'You already have this plan active'];
            return back()->withNotify($notify);
        }

        if ($request->payment_type == Status::WALLET_PAYMENT) {
            if ($plan->price > $user->balance) {
                $notify[] = ['error', 'You don\'t have enough balance to purchase this plan'];
                return back()->withNotify($notify);
            }

            // update purchase plan
            self::updatePurchaseData($plan);

            $notify[] = ['success', 'Plan purchased successfully'];
            return back()->withNotify($notify);
        }

        if ($request->payment_type == Status::GATEWAY_PAYMENT) {
            session()->put('PURCHASE_PLAN', $plan->id);
            return to_route('user.deposit.index');
        }
    }

    public static function updatePurchaseData($plan, $user = null, $method_code = 0)
    {
        if (!$user) {
            $user = auth()->user();
        }

        if ($plan->recurring_type == Status::MONTHLY_PLAN) {
            $planExpiresAt = Carbon::now()->addMonth();
        } else {
            $planExpiresAt = Carbon::now()->addYear();
        }

        $user->balance -= $plan->price;

        $user->plan_id                 = $plan->id;
        $user->plan_expires_at         = $planExpiresAt;
        $user->plan_api_available      = $plan->api_available;
        $user->plan_scheduled_sms      = $plan->scheduled_sms;
        $user->plan_campaign_available = $plan->campaign;
        $user->daily_sms_limit         = $plan->daily_sms_limit;

        if ($user->available_sms == Status::UNLIMITED) {
            $user->available_sms = $plan->sms_limit;
        } elseif ($plan->sms_limit == Status::UNLIMITED) {
            $user->available_sms = Status::UNLIMITED;
        } else {
            $user->available_sms += $plan->sms_limit;
        }

        if ($user->available_group_limit == Status::UNLIMITED) {
            $user->available_group_limit = $plan->group_limit;
        } elseif ($plan->group_limit == Status::UNLIMITED) {
            $user->available_device_limit = Status::UNLIMITED;
        } else {
            $user->available_group_limit += $plan->group_limit;
        }

        if ($user->available_device_limit == Status::UNLIMITED) {
            $user->available_device_limit = $plan->device_limit;
        } elseif ($plan->device_limit == Status::UNLIMITED) {
            $user->available_device_limit = Status::UNLIMITED;
        } else {
            $user->available_device_limit += $plan->device_limit;
        }

        if ($user->available_contact_limit == Status::UNLIMITED) {
            $user->available_contact_limit = $plan->contact_limit;
        } elseif ($plan->contact_limit == Status::UNLIMITED) {
            $user->available_contact_limit = Status::UNLIMITED;
        } else {
            $user->available_contact_limit += $plan->contact_limit;
        }

        $user->save();

        // update purchase plan
        $purchasePlan               = new PurchasePlan();
        $purchasePlan->user_id      = $user->id;
        $purchasePlan->plan_id      = $plan->id;
        $purchasePlan->price        = $plan->price;
        $purchasePlan->payment_type = $method_code ? Status::GATEWAY_PAYMENT : Status::WALLET_PAYMENT;
        $purchasePlan->method_code  = $method_code;
        $purchasePlan->status       = Status::ENABLE;
        $purchasePlan->save();

        // transaction
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $plan->price;
        $transaction->post_balance = $user->balance;
        $transaction->charge       = 0;
        $transaction->trx_type     = '-';
        $transaction->details      = 'Plan purchase';
        $transaction->trx          = getTrx();
        $transaction->remark       = 'plan_purchase';
        $transaction->save();

        if ($method_code == 0) {
            notify($user, 'PLAN_PURCHASE', [
                'trx' => $transaction->trx,
                'plan_name' => $plan->name,
                'amount' => showAmount($plan->price, currencyFormat: false),
                'post_balance' => showAmount($user->balance, currencyFormat: false),
                'plan_recurring_type' => $plan->recurringtypeName,
                'method_name' => 'Wallet Balance',
            ]);
        }

        if (session('PURCHASE_PLAN')) {
            session()->forget('PURCHASE_PLAN');
        }
    }
}
