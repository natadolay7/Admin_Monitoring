<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserBranchController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.userbranch.index');
    }

    public function add()
    {
        $userBranch = DB::table('user_branch')
            ->where('user_id', Auth::user()->id)
            ->first();

        $branch = DB::table('branch')
            ->where('id', $userBranch->branch_id)
            ->first();


        $branchId  = $branch->id;
        $data = DB::table('role')->where('branch_id', $branchId)->get();
        return view('pages.level.branch.userbranch.form', compact('data'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // ✅ VALIDATION
        $branchId = DB::table('user_branch')->where('user_id', Auth::user()->id)->first();
        $branchId = $branchId->branch_id;

        $request->validate([
            // USER
            'username' => ['required', 'string', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            // COMPANY
            'name'    => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ INSERT USER
            $userId = DB::table('users')->insertGetId([
                'name'       => $request->name,
                'email'      => $request->username,
                'password'   => Hash::make($request->password),
                'created_at' => now(),
                'updated_at' => now(),
                'user_type_id' => 5
            ]);

            // 2️⃣ INSERT COMPANY


            // 3️⃣ INSERT USER_COMPANY
            DB::table('user_branch')->insert([
                'user_id'    => $userId,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),

            ]);

            DB::table('role_user')->insert([
                'user_id'    => $userId,
                'role_id' => $request->role_id,
                'created_at' => now(),
                'updated_at' => now(),

            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Branch & User berhasil ditambahkan');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function datatable()
    {
        $branch = DB::table('user_branch')->where('user_id', Auth::user()->id)->first();
        $branch = $branch->branch_id;

        $query = DB::table('users as u')
            ->leftJoin('user_branch as ub', 'u.id', '=', 'ub.user_id')
            ->leftJoin('role_user as ru', 'ru.user_id', '=', 'u.id')
            ->leftJoin('role as r', 'r.id', '=', 'ru.role_id')
            ->leftJoin('branch as b', 'b.id', '=', second: 'ub.branch_id')
            // ->leftJoin('company as c', 'c.id', '=', 'b.company_id')
            ->where('ub.branch_id', $branch)
            ->select([
                'u.email as username',
                'u.name',
                'b.name as branch_name',
                'b.location',
                'u.created_at',
                'r.title as role'
            ])
            ->orderBy('u.id', 'desc');


        return DataTables::of($query)
            // ->filterColumn('code_company', function ($query, $keyword) {
            //     $query->whereRaw('LOWER(c.code) LIKE ?', ['%' . strtolower($keyword) . '%']);
            // })
            // ->editColumn('email', function ($row) {
            //     return $row->email ?? '-';
            // })
            // ->editColumn('status', function ($row) {
            //     return $row->status == 1
            //         ? '<span class="badge bg-success">Active</span>'
            //         : '<span class="badge bg-secondary">Data Lama</span>';
            // })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary">Edit</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
