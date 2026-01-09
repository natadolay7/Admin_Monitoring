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
        $userBranch = DB::table('user_branch')
            ->where('user_id', Auth::user()->id)
            ->first();

        $branch = DB::table('branch')
            ->where('id', $userBranch->branch_id)
            ->first();

        $companyId = $branch->company_id;
        $branchId  = $branch->id;

        $query = DB::table('role')->where('branch_id', $branchId)
            ->select([
                'title',
                'id',

            ]);

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('action', function ($row) {
                return '<a href="' . '#' . '"
                        class="btn btn-sm btn-primary">
                        Download
                    </a>';
            })

            ->rawColumns(['qr', 'status', 'action'])
            ->make(true);
    }

    public function add()
    {
        return view('pages.level.branch.role.form');
    }

    public function store(Request $request)
    {
        $branchId = DB::table('user_branch')->where('user_id', Auth::user()->id)->first();
        $branchId = $branchId->branch_id;


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


}
