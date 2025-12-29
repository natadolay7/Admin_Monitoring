<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserRoleMiddleware
{

    public function handle(Request $request, Closure $next): Response
    {
       $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // default
        $role = 'user';

        // 1️⃣ Super Admin App
        if ($user->user_type_id == 1) {
            $role = 'superadmin_app';
        }

        // 2️⃣ Super Admin Company
        elseif (
            DB::table('user_company')
                ->where('user_id', $user->id)
                ->where('role', 1)
                ->exists()
        ) {
            $role = 'superadmin_company';
        }
        elseif (
            DB::table('user_branch')
                ->where('user_id', $user->id)
                ->where('role', 1)
                ->exists()
        ) {
            $role = 'superadmin_branch';
        }

        // simpan role ke request & session
        $request->attributes->set('role', $role);
        session(['user_role' => $role]);

        return $next($request);
    }
}
