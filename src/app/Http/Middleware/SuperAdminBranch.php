<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminBranch
{
    public function handle(Request $request, Closure $next)
    {
        if (session('user_role') === 'superadmin_branch') {
            return $next($request);
        }

        abort(403, 'Akses khusus Super Admin App');
    }
}

