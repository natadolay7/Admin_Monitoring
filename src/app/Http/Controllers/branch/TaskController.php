<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TaskController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.tasks.index');
    }

    public function datatable()
    {
        // $userBranch = DB::table('user_branch')
        //     ->where('user_id', Auth::id())
        //     ->first();

        // if (!$userBranch) {
        //     return response()->json([
        //         'data' => []
        //     ]);
        // }

        // $branch = DB::table('branch')
        //     ->where('id', $userBranch->branch_id)
        //     ->first();

        // if (!$branch) {
        //     return response()->json([
        //         'data' => []
        //     ]);
        // }

        $branchId = getUserBranchId();

        // Query builder (TANPA get())
        $query = DB::table('task_list as tl')
            ->select(
                'tl.id',
                't.name',
                'tl.branch_id',
                't.task_type_id',
                'tl.note',
                'tt.name as type'
            )
            ->leftJoin('task as t', 'tl.task_id', '=', 't.id')
            ->leftJoin('task_type as tt', 'tt.id', '=', 't.task_type_id')
            //->where('tl.branch_id', $branchId)
        ;

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '
                <button class="btn btn-sm btn-primary" data-id="' . $row->id . '">
                    Edit
                </button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function add()
    {
        $task_type = DB::table('task_type')->where('status', 1)->get();
        $branchId = getUserBranchId();
        $tad = DB::table('user_tad_information as a')
            ->leftJoin('users as b', 'a.user_id',  '=', 'b.id')
            ->select('b.id as user_id', 'b.name')
            ->where('branch_id', $branchId)->get();
        return view('pages.level.branch.tasks.form', compact('task_type' , 'tad'));
    }
}
