<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->middleware('guest')->group(function () {
    Route::controller('LoginController')->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
    });

    Route::controller('RegisterController')->group(function () {
        Route::get('register', 'showRegistrationForm')->name('register');
        Route::post('register', 'register');
        Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
    });

    Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
        Route::get('reset', 'showLinkRequestForm')->name('request');
        Route::post('email', 'sendResetCodeEmail')->name('email');
        Route::get('code-verify', 'codeVerify')->name('code.verify');
        Route::post('verify-code', 'verifyCode')->name('verify.code');
    });

    Route::controller('ResetPasswordController')->group(function () {
        Route::post('password/reset', 'reset')->name('password.update');
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
    });
});

Route::middleware('auth')->name('user.')->group(function () {

    Route::get('user-data', 'User\UserController@userData')->name('data');
    Route::post('user-data-submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {

        Route::namespace('User')->group(function () {

            Route::controller('UserController')->group(function () {
                Route::get('dashboard', 'home')->name('home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');

                //Report
                Route::any('deposit/history', 'depositHistory')->name('deposit.history');
                Route::get('transactions', 'transactions')->name('transactions');
                Route::get('purchase-history', 'purchasedPlans')->name('plan.purchased');

                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');

                // apk
                Route::get('download-apk', 'downloadApk')->name('apk');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
            });

            // Manage Contact
            Route::prefix('contact')->name('contact.')->controller("ContactController")->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('contact/search', "contactSearch")->name('search');
                Route::post('/export', 'exportContact')->name('export');
                Route::post('/status/{id}', 'status')->name('status');
                Route::middleware('has.active.plan')->group(function () {
                    Route::post('/store', 'save')->name('store');
                    Route::post('/update/{id}', 'save')->name('update');
                    Route::post('/import', 'importContact')->name('import');
                });
            });

            // Developer Tools
            Route::controller('DeveloperController')->prefix('developer')->name('developer.')->group(function () {
                Route::get('api/docs', 'apiDocs')->name('api.docs');
                Route::post('regenerate/api/key', 'regenerateApiKey')->name('regenerate.api.key');
            });

            // Manage Group
            Route::name('group.')->prefix('group')->controller("GroupController")->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('contact/view/{id}', 'viewGroupContact')->name('contact.view');
                Route::post('/status/{id}', 'status')->name('status');
                Route::middleware('has.active.plan')->group(function () {
                    Route::post('store', 'saveGroup')->name('store');
                    Route::get('banned', 'banned')->name('banned');
                    Route::post('update/{id}', 'saveGroup')->name('update');
                    Route::post('save/contact/{groupId}', 'contactSaveToGroup')->name('to.contact.save');
                    Route::post('import/contact/{groupId}', 'importContactToGroup')->name('import.contact');
                });
                Route::post('delete/contact/{id}', 'deleteContactFromGroup')->name('delete.contact');
            });

            // Manage Template
            Route::prefix('template')->controller("TemplateController")->name('template.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'save')->name('store');
                Route::post('/update/{id}', 'save')->name('update');
                Route::post('/status/{id}', 'status')->name('status');
            });

            // Manage Device
            Route::prefix('device')->controller("DeviceController")->name('device.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/disconnect/{id}', 'disconnect')->name('disconnect');
            });

            // SMS Manager
            Route::prefix('sms')->controller("SmsController")->name('sms.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('send', 'send')->name('send');
                Route::middleware('has.active.plan')->group(function () {
                    Route::post('send', 'sendSMS')->name('send');
                    Route::post('re-send/{id}', 'reSend')->name('resend');
                });
            });

            // Manage Campaign
            Route::controller('CampaignController')->prefix('campaign')->name('campaign.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::middleware('has.active.plan:plan_campaign_available')->group(function () {
                    Route::post('/store', 'save')->name('store');
                    Route::get('/edit/{id}', 'edit')->name('edit');
                    Route::post('/update/{id}', 'save')->name('update');
                    Route::post('/status/{id}', 'status')->name('status');
                });
            });

            // Batch
            Route::controller('BatchController')->prefix('batch')->name('batch.')->group(function () {
                Route::get('/', 'smsBatch')->name('index');
            });

            // Purchase Plan
            Route::controller('PurchasePlanController')->prefix('purchase-plan')->name('purchase.plan.')->group(function () {
                Route::post('/', 'purchasePlan')->name('insert');
            });
        });

        // Payment
        Route::prefix('deposit')->name('deposit.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
        });
    });
});
