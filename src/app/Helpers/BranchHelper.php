<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('getUserBranchId')) {
    function getUserBranchId()
    {
        $userBranch = DB::table('user_branch')
            ->where('user_id', Auth::id())
            ->first();

        return $userBranch?->branch_id;
    }
}
