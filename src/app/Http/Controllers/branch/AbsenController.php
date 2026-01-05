<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AbsenController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.absen.index');
    }

    public function datatable(Request $request)
    {
        $userBranch = DB::table('user_branch')
            ->where('user_id', Auth::id())
            ->first();

        if (!$userBranch) {
            return response()->json(['data' => []]);
        }

        $branch = DB::table('branch')
            ->where('id', $userBranch->branch_id)
            ->first();

        if (!$branch) {
            return response()->json(['data' => []]);
        }

        $branchId = $branch->id;

        $query = DB::table('user_attendence as ua')
            ->select(
                'ua.id',
                'u.name',
                'ss.name as schedule_name',
                'ua.check_in',
                'ua.check_out',
                'ua.latitude_check_in',
                'ua.latitude_check_out',
                'ss.start_time',
                'ss.end_time',
                'uti.branch_id',
                'ua.created_at',
                DB::raw("
                CASE
                    WHEN ua.check_in IS NOT NULL
                     AND ua.check_in > (DATE(ua.check_in) + ss.start_time)
                    THEN EXTRACT(EPOCH FROM (ua.check_in - (DATE(ua.check_in) + ss.start_time))) / 60
                    ELSE 0
                END AS late_minutes
            ")
            )
            ->leftJoin('users as u', 'u.id', '=', 'ua.users_id')
            ->leftJoin('schedule as s', 's.id', '=', 'ua.schedule_id')
            ->leftJoin('schedule_shift as ss', 'ss.id', '=', 's.schedule_shift_id')
            ->leftJoin('user_tad_information as uti', 'uti.user_id', '=', 'u.id')
            ->where('uti.branch_id', $branchId);

        // ✅ FILTER TANGGAL
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to   = Carbon::parse($request->to_date)->endOfDay();

            $query->whereBetween('ua.created_at', [$from, $to]);
        }

        return DataTables::of($query)
            ->addIndexColumn()

            ->editColumn('late_minutes', function ($row) {
                $minutes = (int) $row->late_minutes;
                if ($minutes <= 0) return '-';

                return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
            })

            ->addColumn('late_status', function ($row) {
                return $row->late_minutes > 0
                    ? '<span class="badge bg-danger">TERLAMBAT</span>'
                    : '<span class="badge bg-success">ON TIME</span>';
            })

            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary" data-id="' . $row->id . '">Detail</button>';
            })

            ->rawColumns(['late_status', 'action'])
            ->make(true);
    }
}
