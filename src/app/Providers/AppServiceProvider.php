<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $checkPermission = function ($type) {

            if (session('user_role') === 'superadmin_branch') {
                return true;
            }

            if (!Auth::check()) {
                return false;
            }

            $branch = DB::table('user_branch')
                ->where('user_id', Auth::id())
                ->first();

            if (!$branch) {
                return false;
            }

            $permission = getMenuPermission(
                Auth::id(),
                $branch->branch_id,
                request()->segment(1)
            );

            return ($permission->{$type} ?? 0) == 1;
        };

        Blade::if('canAdd', fn() => $checkPermission('add'));
        Blade::if('canEdit', fn() => $checkPermission('edit'));
        Blade::if('canDelete', fn() => $checkPermission('delete'));
        Blade::if('canView', fn() => $checkPermission('view'));
    }
}
