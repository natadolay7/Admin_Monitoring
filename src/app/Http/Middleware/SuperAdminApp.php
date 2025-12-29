<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminApp
{
    public function handle(Request $request, Closure $next)
    {
        if (session('user_role') === 'superadmin_app') {
            return $next($request);
        }

        abort(403, 'Akses khusus Super Admin App');
    }
}

