<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class MenuHasRoleController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.menu_has_role.index');
    }

    public function add()
    {
        return view('pages.level.branch.menu_has_role.form');
    }

    public function role()
    {
        $branch = DB::table('user_branch')->where('user_id', Auth::user()->id)->first();
        $branch = $branch->branch_id;

        $roles = DB::table('role')->where('branch_id', $branch)->get();
        return response()->json($roles);
    }

    public function menu()
    {
        $menu = DB::table('menu')->where('status', 1)->get();
        return response()->json($menu);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // ✅ VALIDATION


        DB::beginTransaction();

        try {
            // 1️⃣ INSERT USER
            $rolemenu = DB::table('role_menu')->insertGetId([
                'role_id'       => $request->role_id,
                'menu_id'      => $request->menu_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2️⃣ INSERT COMPANY


            // 3️⃣ INSERT USER_COMPANY
            DB::table('permission_menu')->insert([
                'role_menu_id' => $rolemenu,
                'view'   => $request->has('view') ? 1 : 0,
                'add'    => $request->has('add') ? 1 : 0,
                'edit'   => $request->has('edit') ? 1 : 0,
                'delete' => $request->has('delete') ? 1 : 0,
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
        $userBranch = DB::table('user_branch')
            ->where('user_id', Auth::user()->id)
            ->first();

        $branch = DB::table('branch')
            ->where('id', $userBranch->branch_id)
            ->first();

        $companyId = $branch->company_id;
        $branchId  = $branch->id;

        $query = DB::table('role_menu as rm')
            ->selectRaw('
            u.name as name_user,
            rm.id as role_menu_id,
            r.title as role,
            m.id as menu_id,
            m.name as menu_name,
            pm.view,
            pm.add,
            pm.edit,
            pm.delete,
            m.url
        ')
            ->leftJoin('permission_menu as pm', 'pm.role_menu_id', '=', 'rm.id')
            ->leftJoin('role as r', 'r.id', '=', 'rm.role_id')
            ->leftJoin('menu as m', 'm.id', '=', 'rm.menu_id')
            ->leftJoin('role_user as ru', 'ru.role_id', '=', 'r.id')
            ->leftJoin('users as u', 'ru.user_id', '=', 'u.id')
            ->leftJoin('user_branch as ub', 'u.id', '=', 'ub.user_id')
            ->where('ub.branch_id', $branchId)
            ->orderBy('m.id')
            ->orderBy('rm.id');

        return DataTables::of($query)

            // ICON VIEW
            ->editColumn('view', function ($row) {
                return $row->view
                    ? '<i class="bi bi-check-circle-fill text-success"></i>'
                    : '<i class="bi bi-x-circle-fill text-danger"></i>';
            })

            ->editColumn('add', function ($row) {
                return $row->add
                    ? '<i class="bi bi-check-circle-fill text-success"></i>'
                    : '<i class="bi bi-x-circle-fill text-danger"></i>';
            })

            ->editColumn('edit', function ($row) {
                return $row->edit
                    ? '<i class="bi bi-check-circle-fill text-success"></i>'
                    : '<i class="bi bi-x-circle-fill text-danger"></i>';
            })

            ->editColumn('delete', function ($row) {
                return $row->delete
                    ? '<i class="bi bi-check-circle-fill text-success"></i>'
                    : '<i class="bi bi-x-circle-fill text-danger"></i>';
            })

            // WAJIB
            ->rawColumns(['view', 'add', 'edit', 'delete'])
            ->make(true);
    }
}
