<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\CronController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Admin\Auth\LoginController;

// 👇 Import your user controllers
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\User\DeveloperController;
use App\Http\Controllers\User\ProfileController;

/*
|--------------------------------------------------------------------------
| Debug & Utility Routes
|--------------------------------------------------------------------------
*/

// Debug DB connection
Route::get('/debug-db', function () {
    $canConnect = 'Not tested';
    try {
        DB::connection()->getPdo();
        $canConnect = '✅ Connected';
    } catch (\Exception $e) {
        $canConnect = '❌ ' . $e->getMessage();
    }

    return response()->json([
        'env_values'     => [
            'host'     => env('DB_HOST'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
        ],
        'env_file_exists' => file_exists(base_path('.env')),
        'env_sample'      => substr(file_get_contents(base_path('.env')), 0, 100),
        'can_connect'     => $canConnect,
    ]);
});

// Placeholder image
Route::get('/placeholder-image/{size}', [SiteController::class, 'placeholderImage'])
    ->where('size', '^[0-9]+x[0-9]+$')
    ->name('placeholder.image');

// Redirect after activation
Route::get('/redirect-after-activation', fn() => redirect('/login'))
    ->name('redirect.home');

// Cron
Route::get('/cron', [CronController::class, 'cron'])->name('cron');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public Frontend
|--------------------------------------------------------------------------
*/
Route::get('/',            [SiteController::class, 'index'])->name('home');
Route::get('/contact',     [SiteController::class, 'contact'])->name('contact');
Route::post('/contact',    [SiteController::class, 'contactSubmit'])->name('contact.submit');
Route::get('/blog',        [SiteController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [SiteController::class, 'blogDetails'])->name('blog.details');
Route::get('/pricing',     [SiteController::class, 'pricing'])->name('pricing');
Route::post('/subscribe',  [SiteController::class, 'subscribe'])->name('subscribe');
Route::get('/cookie-policy',[SiteController::class, 'cookiePolicy'])->name('cookie.policy');
Route::get('/cookie/accept',[SiteController::class, 'cookieAccept'])->name('cookie.accept');
Route::get('/language/{lang?}',[SiteController::class, 'changeLanguage'])->name('language.change');
Route::get('/policy/{slug}', [SiteController::class, 'policyPages'])->name('policy.pages');

/*
|--------------------------------------------------------------------------
| Authenticated “User” Area
|--------------------------------------------------------------------------
*/
Route::prefix('user')
     ->name('user.')
     ->middleware(['auth', 'check.status', 'registration.complete'])
     ->group(function() {

    // Dashboard
    Route::get('dashboard',      [UserController::class, 'home'])->name('home');

    // Developer Tools
    Route::get('developer/api/docs',[DeveloperController::class, 'apiDocs'])->name('developer.api.docs');

    // Tickets
    Route::get('ticket',       [TicketController::class, 'index'])->name('ticket.index');
    Route::get('ticket/open',  [TicketController::class, 'open'])->name('ticket.open');
    Route::post('ticket/store', [TicketController::class, 'store'])->name('ticket.store');

    // Profile Settings
    Route::get('profile/setting',[ProfileController::class, 'profile'])->name('profile.setting');
    Route::post('profile/setting',[ProfileController::class, 'submitProfile'])->name('profile.setting.submit');

    // Change Password
    Route::get('change/password',  [ProfileController::class, 'changePassword'])->name('change.password');
    Route::post('change/password', [ProfileController::class, 'submitPassword'])->name('change.password.submit');
});

/*
|--------------------------------------------------------------------------
| Catch-All Pages
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', [SiteController::class, 'pages'])
     ->where('slug', '^(?!admin|login|register|cron|debug-db|placeholder-image|redirect-after-activation|subscribe|contact|cookie).*$')
     ->name('pages');
