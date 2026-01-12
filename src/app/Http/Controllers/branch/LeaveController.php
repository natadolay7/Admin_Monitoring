<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class LeaveController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.leave.index');
    }

    public function datatable(Request $request)
    {
        $userBranch = DB::table('user_branch')
            ->where('user_id', Auth::user()->id)
            ->first();

        $branch = DB::table('branch')
            ->where('id', $userBranch->branch_id)
            ->first();

        $branchId  = $branch->id;


        $query = DB::table('leave_new')
            ->where('branch_id',  $branchId)
            ->select([
                'id',
                'code',
                'type_leave',
                'date_request',
                'date_start',
                'date_end',
                'status'
            ]);

        // FILTER TANGGAL
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date_start', [
                $request->start_date,
                $request->end_date
            ]);
        }

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                return '
                <a href="' . url('/leave/edit/' . $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">Delete</button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
