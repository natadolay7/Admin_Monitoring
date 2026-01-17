<?php

namespace App\Http\Controllers\adminapp;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;


class BranchController extends Controller
{
    public function index()
    {
        return view('pages.level.app.branch.index');
    }

    public function datatable()
    {
        $query = DB::table('branch as b')
            ->leftJoin('user_branch as ub', 'ub.branch_id', '=', 'b.id')
            ->leftJoin('users as u', 'u.id', '=', 'ub.user_id')
            ->leftJoin('company as c', 'c.id', '=', 'b.company_id')
            ->select([
                'b.id',
                'b.code',
                'b.id',
                'b.name as branch_name',
                'b.location',
                'u.email',
                'b.status',
                'b.company_id',
                'c.name as company',
            ])
            ->where('ub.role', 1)
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
                $editUrl = url('v1/branch/edit/' . $row->id);

                return '
                <div class="d-flex gap-2">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-primary">
                         Edit
                    </a>

                    <button
                        class="btn btn-sm btn-danger btn-delete"
                        data-id="' . $row->id . '"

                    >
                         Delete
                    </button>
                </div>
            ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function add()
    {
        return view('pages.level.app.branch.form');
    }

    public function edit($id)
    {
        $data = DB::table('branch as b')->where('id', $id)->select('b.*')->first();
        $ub = DB::table('user_branch')->where('branch_id', $id)->first();
        if ($ub) {
            $data2 = DB::table('users')->where('id', $ub->user_id)->first();
            # code...
        } else {
            $data2 = '';
        }
        return view('pages.level.app.branch.form', compact('data', 'data2'));
    }

    public function store(Request $request)
    {

        $companyId = $request->company;

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

    public function update(Request $request, $id)
    {
        // Ambil relasi user_branch
        $userBranch = DB::table('user_branch')
            ->where('branch_id', $id)
            ->first();

        $userId = $userBranch?->user_id;

        // VALIDASI KHUSUS UPDATE
        $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'password' => [
                'nullable',
                'string',
                'min:6',
            ],

            'branch_name' => ['required', 'string', 'max:255'],

            'branch_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('branch', 'code')->ignore($id),
            ],

            'longitude' => ['required', 'string', 'max:50'],
            'latitude'  => ['required', 'string'],
            'radius'    => ['required', 'integer'],
            'building'  => ['required', 'string'],
            'timezone'  => ['required'],
            'location'  => ['required', 'string'],
        ]);

        DB::beginTransaction();

        try {

            // 1️⃣ UPDATE / INSERT USER
            $user = $userId ? DB::table('users')->where('id', $userId)->first() : null;

            if ($user) {
                DB::table('users')->where('id', $userId)->update([
                    'email'      => $request->username,
                    'password'   => $request->password
                        ? Hash::make($request->password)
                        : $user->password,
                    'updated_at' => now(),
                ]);
            } else {
                $userId = DB::table('users')->insertGetId([
                    'name'       => $request->branch_name,
                    'email'      => $request->username,
                    'password'   => Hash::make($request->password),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2️⃣ UPDATE BRANCH
            DB::table('branch')->where('id', $id)->update([
                'company_id' => $request->company,
                'name'       => $request->branch_name,
                'code'       => $request->branch_code,
                'longitude'  => $request->longitude,
                'latitude'   => $request->latitude,
                'radius'     => $request->radius,
                'location'   => $request->location,
                'building'   => $request->building,
                'timezone'   => $request->timezone,
                'updated_at' => now(),
                'status' => 1
            ]);

            // 3️⃣ UPDATE / INSERT USER_BRANCH
            DB::table('user_branch')->updateOrInsert(
                ['branch_id' => $id],
                [
                    'user_id'    => $userId,
                    'role'       => 1,
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Branch & User berhasil diperbarui');
        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            // Ambil relasi user_company
            $userCompany = DB::table('user_branch')
                ->where('branch_id', $id)
                ->first();

            // Hapus user jika ada relasi
            if ($userCompany) {
                DB::table('users')
                    ->where('id', $userCompany->user_id)
                    ->delete();

                DB::table('user_branch')
                    ->where('company_id', $id)
                    ->delete();
            }

            // Hapus semua branch
            DB::table('branch')
                ->where('id', $id)
                ->delete();

            // Hapus company

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
