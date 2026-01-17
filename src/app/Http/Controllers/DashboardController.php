<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     return view('pages.dashboard.index');
    // }
    public function index(Request $request)
    {
        // Default 1 bulan terakhir
        $startDate = $request->start_date ?? now()->subMonth()->toDateString();
        $endDate = $request->end_date ?? now()->toDateString();

        /* =======================
           DATA ABSENSI
        ======================= */
        $absenQuery = DB::table('user_attendence as ua')
            ->select(
                'ua.id',
                'u.name',
                'ss.name as schedule_name',
                'ua.check_in',
                'ua.check_out',
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
            ->whereBetween('ua.created_at', [$startDate, $endDate]);

        $absen = $absenQuery->get();

        $totalAbsen = $absen->count();
        $late = $absen->where('late_minutes', '>', 0)->count();
        $ontime = $absen->where('late_minutes', 0)->count();
        $notCheckout = $absen->whereNull('check_out')->count();
        $avgLate = round($absen->avg('late_minutes'), 1);

        /* =======================
           DATA PATROLI
        ======================= */
        $patroli = DB::table('patroli_report as pr')
            ->select(
                'pr.id',
                'u.name as tad_name',
                'mp.nama_lokasi',
                'pr.deskripsi',
                'mp.branch_id',
                'b.name as branch_name',
                'pr.created_at'
            )
            ->leftJoin('master_patroli as mp', 'mp.id', '=', 'pr.id_patroli')
            ->leftJoin('branch as b', 'mp.branch_id', '=', 'b.id')
            ->leftJoin('users as u', 'u.id', '=', 'pr.user_id')
            ->whereBetween('pr.created_at', [$startDate, $endDate])
            ->orderBy('pr.created_at', 'desc')
            ->get();

        $totalPatroli = $patroli->count();

        $absenPerHari = DB::table('user_attendence')
            ->selectRaw("DATE(created_at) as tanggal, COUNT(*) as total")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $lateStats = DB::table('user_attendence as ua')
            ->selectRaw("
        SUM(
          CASE
            WHEN ua.check_in > (DATE(ua.check_in) + ss.start_time)
            THEN 1 ELSE 0
          END
        ) as terlambat,
        SUM(
          CASE
            WHEN ua.check_in <= (DATE(ua.check_in) + ss.start_time)
            THEN 1 ELSE 0
          END
        ) as tepat_waktu
    ")
            ->leftJoin('schedule as s', 's.id', '=', 'ua.schedule_id')
            ->leftJoin('schedule_shift as ss', 'ss.id', '=', 's.schedule_shift_id')
            ->whereBetween('ua.created_at', [$startDate, $endDate])
            ->first();

        $patroliPerHari = DB::table('patroli_report')
            ->selectRaw("DATE(created_at) as tanggal, COUNT(*) as total")
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        return view('pages.dashboard.index', compact(
            'absen',
            'patroli',
            'totalAbsen',
            'late',
            'ontime',
            'notCheckout',
            'avgLate',
            'totalPatroli',
            'startDate',
            'endDate',
            'absenPerHari',
            'lateStats',
            'patroliPerHari'

        ));
    }
}
