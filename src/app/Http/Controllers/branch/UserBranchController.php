<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

use function Symfony\Component\Clock\now;

class UserBranchController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.userbranch.index');
    }

    public function add()
    {
        if (checkPermission('add')) {
            $branchId = getUserBranchId();
            $data2 = DB::table('role')->where('branch_id', $branchId)->get();
            return view('pages.level.branch.userbranch.form', compact('data2'));
        } else {
            echo "tidak punya akses";
        }
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
        $branchId = getUserBranchId();

        $query = DB::table('users as u')
            ->leftJoin('user_branch as ub', 'u.id', '=', 'ub.user_id')
            ->leftJoin('role_user as ru', 'ru.user_id', '=', 'u.id')
            ->leftJoin('role as r', 'r.id', '=', 'ru.role_id')
            ->leftJoin('branch as b', 'b.id', '=', second: 'ub.branch_id')
            // ->leftJoin('company as c', 'c.id', '=', 'b.company_id')
            ->where('ub.branch_id', $branchId)
            ->select([
                'u.id',
                'u.email as username',
                'u.name',
                'b.name as branch_name',
                'b.location',
                'u.created_at',
                'r.title as role'
            ])
            ->orderBy('u.id', 'desc');


        return DataTables::of($query)

            ->addColumn('action', function ($row) {
                $btn = '';

                if (checkPermission('edit')) {
                    $btn .= '<a href="' . url('/core/users/edit/' . $row->id) . '"
                            class="btn btn-sm btn-primary me-1">
                            Edit
                         </a>';
                }

                if (checkPermission('delete')) {
                    $btn .= '<button class="btn btn-sm btn-danger btn-delete"
                            data-id="' . $row->id . '">
                            Delete
                         </button>';
                };

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit($id)
    {
        if (!checkPermission('edit')) {
            abort(403, 'Tidak punya akses');
        }

        $branchId = getUserBranchId();

        $data = DB::table('users as u')
            ->select('u.*', 'ru.role_id')
            ->leftJoin('user_branch as ub', 'u.id', '=', 'ub.user_id')
            ->leftJoin('role_user as ru', 'ru.user_id', '=', 'u.id')
            ->where('u.id', $id)
            ->first();
        $data2 = DB::table('role')->where('branch_id', $branchId)->get();
        if (!$data) {
            abort(404);
        }

        return view('pages.level.branch.userbranch.form', compact('data', 'data2'));
    }

    public function update(Request $request, $id)
    {
        if (!checkPermission('edit')) {
            abort(403, 'Tidak punya akses');
        }

        $request->validate([
            'username' => 'required',
            'name'     => 'required',
            'role_id'  => 'required',
        ]);

        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            abort(404);
        }

        // Update users table
        $updateData = [
            'email' => $request->username,
            'name'  => $request->name,
        ];

        // Jika password diisi, baru diupdate
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        DB::table('users')->where('id', $id)->update($updateData);

        // Update role_user
        DB::table('role_user')->where('user_id', $id)->delete();

        DB::table('role_user')->insert([
            'user_id' => $id,
            'role_id' => $request->role_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'User berhasil diperbarui');
    }
}
