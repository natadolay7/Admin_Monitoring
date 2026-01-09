<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('getMenuPermission')) {
    function getMenuPermission($userId, $branchId, $url)
    {
        return DB::table('role_menu as rm')
            ->select(
                'u.name as name_user',
                'rm.id as role_menu_id',
                'r.title as role',
                'm.id as menu_id',
                'm.name as menu_name',
                'pm.view',
                'pm.delete',
                'pm.add',
                'pm.edit',
                'm.url',
                'u.id as user_id'
            )
            ->leftJoin('permission_menu as pm', 'pm.role_menu_id', '=', 'rm.id')
            ->leftJoin('role as r', 'r.id', '=', 'rm.role_id')
            ->leftJoin('menu as m', 'm.id', '=', 'rm.menu_id')
            ->leftJoin('role_user as ru', 'ru.role_id', '=', 'r.id')
            ->leftJoin('users as u', 'u.id', '=', 'ru.user_id')
            ->leftJoin('user_branch as ub', 'u.id', '=', 'ub.user_id')
            ->where('ub.branch_id', $branchId)
            ->where('u.id', $userId)
            ->where('m.url', $url)
            ->orderBy('m.id')
            ->orderBy('rm.id')
            ->first();   // 👈 hanya 1 data
    }
}
