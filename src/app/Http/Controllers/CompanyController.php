<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class CompanyController extends Controller
{
    public function index()
    {
        return view('pages.company.index');
    }

    public function datatable()
    {
        $query = DB::table('company as c')
            ->leftJoin('user_company as uc', 'c.id', '=', 'uc.company_id')
            ->leftJoin('users as u', 'u.id', '=', 'uc.user_id')
            ->select([
                'c.code as code_company',
                'u.email',
                'c.status'
            ])
            ->orderBy('c.id', 'desc');

        return DataTables::of($query)
            ->filterColumn('code_company', function ($query, $keyword) {
                $query->whereRaw('LOWER(c.code) LIKE ?', ['%' . strtolower($keyword) . '%']);
            })
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
        return view('pages.company.form');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // ✅ VALIDATION
        $request->validate([
            // USER
            'username' => ['required', 'string', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],

            // COMPANY
            'company_name'    => ['required', 'string', 'max:255'],
            'company_code'    => ['required', 'string', 'max:100', 'unique:company,code'],
            'company_email'   => ['required', 'email', 'unique:company,email'],
            'company_contact' => ['required', 'string', 'max:50'],
            'company_address' => ['required', 'string'],
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ INSERT USER
            $userId = DB::table('users')->insertGetId([
                'name'       => $request->company_name,
                'email'      => $request->username,
                'password'   => Hash::make($request->password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2️⃣ INSERT COMPANY
            $companyId = DB::table('company')->insertGetId([
                'name'       => $request->company_name,
                'code'       => $request->company_code,
                'email'      => $request->company_email,
                'contact'    => $request->company_contact,
                'address'    => $request->company_address,
                'status'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3️⃣ INSERT USER_COMPANY
            DB::table('user_company')->insert([
                'user_id'    => $userId,
                'company_id' => $companyId,
                'role' => 1,

            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Company & User berhasil ditambahkan');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
