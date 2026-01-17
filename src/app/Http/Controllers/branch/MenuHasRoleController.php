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
        $branchId = getUserBranchId();
        $roles = DB::table('role')->where('branch_id', $branchId)->get();
        return response()->json($roles);
    }

    public function menu()
    {
        $menu = DB::table('menu')->where('status', 1)->get();
        return response()->json($menu);
    }

    public function store(Request $request)
    {

        $request->validate([
            'role_id' => 'required|exists:role,id',
            'menu_id' => 'required|exists:menu,id',
        ]);

        //  dd($request->all());

        DB::beginTransaction();

        try {
            $rolemenu = DB::table('role_menu')->where([
                'role_id' => $request->role_id,
                'menu_id' => $request->menu_id,
            ])->value('id');

            if (!$rolemenu) {
                $rolemenu = DB::table('role_menu')->insertGetId([
                    'role_id' => $request->role_id,
                    'menu_id' => $request->menu_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('permission_menu')->updateOrInsert(
                ['role_menu_id' => $rolemenu],
                [
                    'view' => $request->has('view') ? 1 : 0,
                    'add' => $request->has('add') ? 1 : 0,
                    'edit' => $request->has('edit') ? 1 : 0,
                    'delete' => $request->has('delete') ? 1 : 0,
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            return back()->with('success', 'Permission berhasil disimpan');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function datatable()
    {
        $branchId = getUserBranchId();

        $query = DB::table('role_menu as rm')
            ->selectRaw('

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

            ->where('r.branch_id', $branchId)
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
