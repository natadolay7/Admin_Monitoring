<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class BranchController extends Controller
{
    public function index()
    {
        return view('pages.branch.index');
    }

    public function datatable()
    {
        $company_id = DB::table('user_company')->where('user_id', Auth::user()->id)->first();
        $companyid = $company_id->company_id;

        $query = DB::table('branch as b')
            ->leftJoin('user_branch as ub', 'ub.branch_id', '=', 'b.id')
            ->leftJoin('users as u', 'u.id', '=', 'ub.user_id')
            ->leftJoin('company as c', 'c.id', '=', 'b.company_id')
            ->where('b.company_id', operator: $companyid)
            ->select([
                'b.code',
                'b.id',
                'b.name as branch_name',
                'b.location',
                'u.email',
                'b.status',
                'b.company_id',
                'c.name as company',
            ])
            ->orderBy('b.id', 'desc');


        return DataTables::of($query)
            // ->filterColumn('code_company', function ($query, $keyword) {
            //     $query->whereRaw('LOWER(c.code) LIKE ?', ['%' . strtolower($keyword) . '%']);
            // })
            ->editColumn('email', function ($row) {
                return $row->email ?? '-';
            })
            ->editColumn('status', function ($row) {
                return $row->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Data Lama</span>';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary">Edit</button>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function add()
    {
        return view('pages.branch.form');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // ✅ VALIDATION
        $companyId = DB::table('user_company')->where('user_id', Auth::user()->id)->first();
        $companyId = $companyId->company_id;

        $request->validate([
            // USER
            'username' => ['required', 'string', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],

            // COMPANY

            'branch_name'    => ['required', 'string', 'max:255'],
            'branch_code'    => ['required', 'string', 'max:100', 'unique:branch,code'],
            'longitude' => ['required', 'string', 'max:50'],
            'latitude' => ['required', 'string'],
            'radius' => ['required', 'integer'],
            'building' => ['required', 'string'],
            'timezone' => ['required', 'string'],
            'location' => ['required', 'string'],

        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ INSERT USER
            $userId = DB::table('users')->insertGetId([
                'name'       => $request->branch_name,
                'email'      => $request->username,
                'password'   => Hash::make($request->password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2️⃣ INSERT COMPANY
            $branchId = DB::table('branch')->insertGetId([
                'company_id' => $companyId,
                'name'       => $request->branch_name,
                'code'       => $request->branch_code,
                'longitude'      => $request->longitude,
                'latitude'    => $request->latitude,
                'radius'    => $request->radius,
                'location'    => $request->location,
                'building'    => $request->building,
                'timezone'    => $request->timezone,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3️⃣ INSERT USER_COMPANY
            DB::table('user_branch')->insert([
                'user_id'    => $userId,
                'branch_id' => $branchId,
                'role' => 1,
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
}
