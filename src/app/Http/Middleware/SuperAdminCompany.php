<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminCompany
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array(session('user_role'), [
            'superadmin_app',
            'superadmin_company'
        ])) {
            return $next($request);
        }

        abort(403, 'Akses khusus Admin Company');
    }
}
