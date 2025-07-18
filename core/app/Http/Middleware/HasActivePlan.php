<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasActivePlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next, $module = null): Response
    {
        $plan = auth()->user()->activePlan();
        $user = auth()->user();
        // check for the active plan
        if (!$plan) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You don\'t have any active plan'
                ]);
            }
            $notify[] = ['error', 'You don\'t have any active plan'];
            return back()->withNotify($notify);
        }

        if ($module && !$user->$module) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => "User doesn't have " . keyToTitle($module)
                ]);
            }

            $notify[] = ['error', "User doesn't have " . keyToTitle($module)];
            return back()->withNotify($notify);
        }

        return $next($request);
    }
}
