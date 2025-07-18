<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api')->name('api.')->prefix('v1')->group(function () {

    Route::namespace('Auth')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::post('login', 'login');
        });

        Route::controller('ForgotPasswordController')->group(function () {
            Route::post('password/email', 'sendResetCodeEmail');
            Route::post('password/verify-code', 'verifyCode');
            Route::post('password/reset', 'reset');
        });
    });

    Route::middleware('auth.api:sanctum')->group(function () {

        Route::post('logout', "User\Auth\LoginController@logout");

        Route::namespace('User\Auth')->middleware('check.status')->group(function () {
            Route::controller('LoginController')->group(function () {
                Route::post('add-device', "addDevice");
            });
        });
    });

    Route::namespace('User')->group(function () {
        Route::middleware(["auth.api:sanctum"])->group(function () {
            Route::prefix('message')->controller("SmsController")->group(function () {
                Route::post('/received', "received");
                Route::post('/update/{id}', "update");
            });
        });

        // API Implementation
        Route::controller('SmsController')->prefix('sms')->middleware('apiKey')->group(function () {
            Route::post('/send', 'send')->name('sms.send');
            Route::get('/send/get', 'sendViaGet')->name('sms.send.get');
        });

        // Pusher API
        Route::controller('PusherController')->group(function () {
            Route::post('pusher/auth', 'authenticationApp')->middleware('auth.api:sanctum');
            Route::post('pusher/auth/{socketId}/{channelName}', 'authentication');
        });
    });
});
