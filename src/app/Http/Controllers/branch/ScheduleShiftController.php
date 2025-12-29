<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ScheduleShiftController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.shift.index');
    }

    public function datatable()
    {
        $branch = DB::table('user_branch')->where('user_id', Auth::user()->id)->first();
        $branch = DB::table('branch')->where('id', $branch->branch_id)->first();

        $branch = $branch->company_id;

        $query = DB::table('schedule_shift as a')
            ->where('a.company_id', $branch)
            ->select([
                'a.code as code',
                'a.name',
                'a.start_time',
                'a.end_time',
                'a.created_at'
            ])
            ->orderBy('a.id', 'desc');


        return DataTables::of($query)
            // ->filterColumn('code_company', function ($query, $keyword) {
            //     $query->whereRaw('LOWER(c.code) LIKE ?', ['%' . strtolower($keyword) . '%']);
            // })
            // ->editColumn('email', function ($row) {
            //     return $row->email ?? '-';
            // })
            // ->editColumn('status', function ($row) {
            //     return $row->status == 1
            //         ? '<span class="badge bg-success">Active</span>'
            //         : '<span class="badge bg-secondary">Data Lama</span>';
            // })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary">Edit</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function add()
    {
        return view('pages.level.branch.shift.form');
    }

    public function store(Request $request)
    {
        $branch = DB::table('user_branch')->where('user_id', Auth::user()->id)->first();
        $branch = DB::table('branch')->where('id', $branch->branch_id)->first();

        $companyId = $branch->company_id;
        // ✅ Validasi
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:100',
            'start_hour' => 'required',
            'start_minute' => 'required',
            'end_hour' => 'required',
            'end_minute' => 'required',
        ]);

        // ✅ Gabungkan jam & menit
        $startTime = $request->start_hour . ':' . $request->start_minute . ':00';
        $endTime   = $request->end_hour . ':' . $request->end_minute . ':00';

        // ✅ Validasi logika waktu
        if ($endTime <= $startTime) {
            return back()->withErrors([
                'end_time' => 'End Time harus lebih besar dari Start Time'
            ])->withInput();
        }

        // ✅ Simpan ke database
        DB::table('schedule_shift')->insert([
            'code' => $request->code,
            'name' => $request->name,
            'start_time' => $startTime, // time(0)
            'end_time' => $endTime,     // time(0)
            'created_at' => now(),
            'updated_at' => now(),
            'company_id' => $companyId
            // 'company_id' => auth()->user()->company_id ?? null,
        ]);

        return redirect()->back()->with('success', 'Shift berhasil disimpan');
    }

    public function generateScheduleBalanced()
    {
        DB::beginTransaction();

        try {
            /** =====================
             * Branch & company
             * ===================== */
            $userBranch = DB::table('user_branch')
                ->where('user_id', Auth::user()->id)
                ->first();

            $branch = DB::table('branch')
                ->where('id', $userBranch->branch_id)
                ->first();

            $companyId = $branch->company_id;
            $branchId  = $branch->id;

            /** =====================
             * Ambil shift
             * ===================== */
            $shifts = DB::table('schedule_shift')
                ->where('company_id', $companyId)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            if (count($shifts) < 2) {
                throw new \Exception('Minimal 2 shift diperlukan');
            }

            /** =====================
             * Ambil user
             * ===================== */
            $users = DB::table('users as u')
                ->leftJoin('user_tad_information as uti', 'u.id', '=', 'uti.user_id')
                ->where('uti.branch_id', $branchId)
                ->pluck('u.id')
                ->toArray();

            shuffle($users); // 🔀 random user

            $shiftCount = count($shifts);

            /** =====================
             * Generate schedule
             * ===================== */
            foreach ($users as $userIndex => $userId) {

                // 🔀 libur random (1–7)
                $holidayDay = rand(1, 7);

                // ⚖️ shift awal adil
                $startShiftIndex = $userIndex % $shiftCount;

                $workDayCounter = 0; // hitung hari kerja

                for ($day = 1; $day <= 7; $day++) {

                    // ===== LIBUR =====
                    if ($day == $holidayDay) {
                        $data = [
                            'users_id'          => $userId,
                            'schedule_shift_id' => null,
                            'day'               => $day,
                            'holiday'           => 1,
                            'updated_at'        => now(),
                        ];
                    } else {
                        /**
                         * Rotasi berdasarkan HARI KERJA
                         * Ganti shift tiap 2 hari kerja
                         */
                        $rotationStep = intdiv($workDayCounter, 2);
                        $shiftIndex   = ($startShiftIndex + $rotationStep) % $shiftCount;

                        $data = [
                            'users_id'          => $userId,
                            'schedule_shift_id' => $shifts[$shiftIndex],
                            'day'               => $day,
                            'holiday'           => 0,
                            'updated_at'        => now(),
                        ];

                        $workDayCounter++; // hanya naik kalau kerja
                    }

                    /** =====================
                     * Upsert manual
                     * ===================== */
                    $exists = DB::table('schedule')
                        ->where('users_id', $userId)
                        ->where('day', $day)
                        ->first();

                    if ($exists) {
                        DB::table('schedule')
                            ->where('id', $exists->id)
                            ->update($data);
                    } else {
                        $data['created_at'] = now();
                        DB::table('schedule')->insert($data);
                    }
                }
            }

            DB::commit();

           return redirect()->back()->with('success', 'Berhasil Tergenerate');
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
