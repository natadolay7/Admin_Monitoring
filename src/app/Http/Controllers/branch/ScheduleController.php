<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.schedule.index');
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

        // Ambil data schedule
        $query = DB::table('schedule as s')
            ->leftJoin('schedule_shift as ss', 's.schedule_shift_id', '=', 'ss.id')
            ->leftJoin('users as u', 's.users_id', '=', 'u.id')
            ->leftJoin('user_tad_information as uti', 'u.id', '=', 'uti.user_id')
            ->leftJoin('branch as b', 'b.id', '=', 'uti.branch_id')
            ->where('uti.branch_id', $branchId)
            ->select([
                'u.name as nama_tad',
                'uti.branch_id',
                'ss.name as nama_shift',
                's.day',
                'ss.start_time',
                'ss.end_time',
                's.holiday',
                's.created_at',
            ])
            ->orderBy('s.day')
            ->get();

        /**
         * Konversi day (1–7) → tanggal
         * 1 = Senin
         */
       $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);

        return DataTables::of($query)
            ->addIndexColumn()

            // Hari (Senin, Selasa, dst)
            ->addColumn('hari', function ($row) use ($startOfWeek) {
                return $startOfWeek
                    ->copy()
                    ->addDays($row->day - 1)
                    ->translatedFormat('l');
            })

            // Tanggal (YYYY-MM-DD)
            ->addColumn('tanggal', function ($row) use ($startOfWeek) {
                return $startOfWeek
                    ->copy()
                    ->addDays($row->day - 1)
                    ->format('Y-m-d');
            })

            // Jam Shift
            ->editColumn('start_time', fn ($row) => substr($row->start_time, 0, 5))
            ->editColumn('end_time', fn ($row) => substr($row->end_time, 0, 5))

            // Status Libur
            ->editColumn('holiday', function ($row) {
                return $row->holiday
                    ? '<span class="badge bg-danger">Libur</span>'
                    : '<span class="badge bg-success">Kerja</span>';
            })

            ->rawColumns(['holiday'])
            ->make(true);
    }
}
