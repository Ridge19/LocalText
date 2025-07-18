<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiKey as ApiKeyModel;

class ApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            $apiKey = $request->apikey;
        } else {
            $header = $request->header();
            if (!array_key_exists('apikey', $header)) {
                return apiResponse(false, 422, null, ['API key must be required on request header']);
            }
            $apiKey = @$header['apikey'][0];
        }

        if (!$apiKey) {
            return apiResponse(false, 422, null, ['API key must be required']);
        }

        $exitsApiKey = ApiKeyModel::where('status', Status::ENABLE)->where('key', $apiKey)->exists();

        if (!$exitsApiKey) {
            return apiResponse(false, 406, null, ['API Key mismatch', 'API key not acceptable']);
        }

        return $next($request);
    }
}
