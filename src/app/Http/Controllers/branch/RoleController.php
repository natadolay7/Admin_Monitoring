<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.role.index');
    }

    public function datatable()
    {
        $branchId = getUserBranchId();

        $query = DB::table('role')->where('branch_id', $branchId)
            ->select([
                'title',
                'id',

            ]);

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('action', function ($row) {
                $btn = '';

                if (checkPermission('edit')) {
                    $btn .= '<a href="' . url('/core/role/edit/' . $row->id) . '"
                            class="btn btn-sm btn-primary me-1">
                            Edit
                         </a>';
                }

                if (checkPermission('delete')) {
                    $btn .= '<button class="btn btn-sm btn-danger btn-delete"
                            data-id="' . $row->id . '">
                            Delete
                         </button>';
                }

                return $btn;
            })

            ->rawColumns(['qr', 'status', 'action'])
            ->make(true);
    }

    public function edit($id)
    {
        if (!checkPermission('edit')) {
            abort(403, 'Tidak punya akses');
        }

        $branchId = getUserBranchId();

        $data = DB::table('role')
            ->where('id', $id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$data) {
            abort(404);
        }

        return view('pages.level.branch.role.form', compact('data'));
    }

    public function add()
    {
        if (checkPermission('add')) {
            return view('pages.level.branch.role.form');
        } else {
            echo "Tidak Punya Akses";
        }
    }

    public function store(Request $request)
    {
        $branchId = getUserBranchId();

        DB::beginTransaction();

        try {
            // 1️⃣ INSERT USER
            $userId = DB::table('role')->insertGetId([
                'title'       => $request->title,
                'created_at' => now(),
                'updated_at' => now(),
                'branch_id' => $branchId
            ]);

            // 2️⃣ INSERT COMPANY


            // 3️⃣ INSERT USER_COMPANY


            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Role berhasil ditambahkan');
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

        DB::beginTransaction();

        try {
            // 1️⃣ INSERT USER
            $userId = DB::table('role')->where('id', $id)->update([
                'title'       => $request->title,
                'updated_at' => now(),
            ]);

            // 2️⃣ INSERT COMPANY


            // 3️⃣ INSERT USER_COMPANY


            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Role berhasil diupdate');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function delete($id)
    {
        if (!checkPermission('delete')) {
            return response()->json(['message' => 'Tidak punya akses'], 403);
        }

        $branchId = getUserBranchId();

        // Ambil semua role_menu.id berdasarkan role_id
        $roleMenuIds = DB::table('role_menu')
            ->where('role_id', $id)
            ->pluck('id');

        // Hapus permission_menu yang terkait
        DB::table('permission_menu')
            ->whereIn('role_menu_id', $roleMenuIds)
            ->delete();

        // Hapus role_menu
        DB::table('role_menu')
            ->where('role_id', $id)
            ->delete();

        // Hapus role
        DB::table('role')
            ->where('id', $id)
            ->where('branch_id', $branchId)
            ->delete();

        return response()->json(['message' => 'Role berhasil dihapus']);
    }
}
