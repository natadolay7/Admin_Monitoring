<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.user.index');
    }

    public function datatable()
    {
        $branch = DB::table('user_branch')->where('user_id', Auth::user()->id)->first();
        $branch = $branch->branch_id;



        $query = DB::table('users as u')
            ->leftJoin('user_tad_information as uti', 'u.id', '=', 'uti.user_id')
            ->leftJoin('branch as b', 'b.id', '=', second: 'uti.branch_id')
            // ->leftJoin('company as c', 'c.id', '=', 'b.company_id')
            ->where('uti.branch_id', $branch)
            ->select([
                'u.id',
                'u.email as username',
                'u.name',
                'b.name as branch_name',
                'b.location',
                'u.created_at'
            ])
            ->orderBy('u.id', 'desc');


        return DataTables::of($query)
            ->addColumn('edit', function ($row) {
                return checkPermission('edit')
                    ? '<a href="' . url('management-users/edit/' . $row->id) . '" class="btn btn-sm btn-primary">Edit</a>'
                    : '';
            })

            ->addColumn('delete', function ($row) {
                return checkPermission('delete')
                    ? '<button class="btn btn-sm btn-danger btn-delete" data-id="'.$row->id.'">Delete</button>'
                    : '';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
    }

    public function add()
    {
        if (checkPermission('add')) {
            return view('pages.level.branch.user.form');
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
            DB::table('user_tad_information')->insert([
                'user_id'    => $userId,
                'branch_id' => $branchId,
                'type_zone' => 1,
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

    public function edit($id)
    {
        if (checkPermission('edit')) {
            $data = DB::table('users as u')->where('id', $id)->first();
            return view('pages.level.branch.user.form_edit', compact('data'));
        } else {
            echo "tidak punya akses";
        }
    }

    public function update(Request $request)
    {
        // VALIDASI
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|unique:users,email,' . $request->id . ',id',
            'password' => 'nullable|min:6',
        ]);

        DB::beginTransaction();

        try {
            $id = $request->id;
            $data = DB::table('users')->where('id', $id)->first();

            // PASSWORD LOGIC
            if (!empty($request->password)) {
                $password = Hash::make($request->password); // password baru
            } else {
                $password = $data->password; // pakai password lama
            }

            DB::table('users')->where('id', $id)->update([
                'name'         => $request->name,
                'email'        => $request->username,
                'password'     => $password,
                'updated_at'   => now(),
                'user_type_id' => 5
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'User berhasil diupdate');
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
            // Hapus relasi dulu (jika ada)
            DB::table('user_tad_information')->where('user_id', $id)->delete();
            // Hapus user
            DB::table('users')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
