<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperAdminBranch
{
    public function handle(Request $request, Closure $next)
    {
        // Superadmin branch bebas akses
        if (session('user_role') === 'superadmin_branch') {
            return $next($request);
        }

        // ✅ WHITELIST URL WAJIB
        $whitelist = [
            '/',
            '/logout',
        ];
        $user_id = Auth::user()->id;


        $currentUrl = trim($request->path(), '/');
        // hasil: v2/management-users

        $allowedUrls = DB::table('role_menu as rm')
            ->join('menu as m', 'm.id', '=', 'rm.menu_id')
            ->join('role_user as ru', 'ru.role_id', '=', 'rm.role_id')
            ->where('ru.user_id', $user_id)
            ->select('m.url')
            ->distinct()
            ->pluck('m.url')
            ->map(fn($url) => trim($url, '/')) // 🔥 NORMALISASI
            ->toArray();

        foreach ($allowedUrls as $url) {
            if (
                $currentUrl === $url ||
                str_starts_with($currentUrl, $url . '/')
            ) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini');
    }
}
