<?php

namespace App\Providers;

use App\Constants\Status;
use App\Lib\Searchable;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;       // ← Add this import
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Builder::mixin(new Searchable);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ──────── Installation Check ────────
        if (! Cache::get('SystemInstalled')) {
            $envFilePath = base_path('.env');
            if (! file_exists($envFilePath) || empty(file_get_contents($envFilePath))) {
                header('Location: install');
                exit;
            }
            Cache::put('SystemInstalled', true);
        }

        // ──────── Global View Share ────────
        View::share('emptyMessage', 'Data not found');

        // ──────── Admin Sidebar Data ────────
        View::composer('admin.partials.sidenav', function ($view) {
            $view->with([
                'bannedUsersCount'           => User::banned()->count(),
                'emailUnverifiedUsersCount'  => User::emailUnverified()->count(),
                'mobileUnverifiedUsersCount' => User::mobileUnverified()->count(),
                'pendingTicketCount'         => SupportTicket::whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY])->count(),
                'pendingDepositsCount'       => Deposit::pending()->count(),
                'updateAvailable'            => version_compare(gs('available_version'), systemDetails()['version'], '>') ? 'v'.gs('available_version') : false,
            ]);
        });

        // ──────── Admin Topnav Data ────────
        View::composer('admin.partials.topnav', function ($view) {
            $notifications = AdminNotification::where('is_read', Status::NO)
                                ->with('user')
                                ->orderByDesc('id')
                                ->limit(10)
                                ->get();

            $view->with([
                'adminNotifications'    => $notifications,
                'adminNotificationCount'=> $notifications->count(),
            ]);
        });

        // ──────── SEO Meta Data ────────
        View::composer('partials.seo', function ($view) {
            $seo = Frontend::where('data_keys', 'seo.data')->first();
            $view->with('seo', $seo ? $seo->data_values : null);
        });

        // ──────── Force HTTPS ────────
        // Temporarily disabled for local development
        // if ( gs('force_ssl') && app()->environment('production') ) {
        //     URL::forceScheme('https');
        // }

        // ──────── Pagination Styling ────────
        Paginator::useBootstrapFive();
    }
}
