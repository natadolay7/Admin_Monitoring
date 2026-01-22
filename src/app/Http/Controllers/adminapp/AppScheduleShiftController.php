<?php

namespace App\Http\Controllers\adminapp;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AppScheduleShiftController extends Controller
{
    public function index()
    {
        return view('pages.level.app.shift.index');
    }

    public function datatable()
    {
        $query = DB::table('schedule_shift as a')

            ->select([
                'a.id',
                'a.code as code',
                'a.name',
                'a.start_time',
                'a.end_time',
                'a.created_at'
            ])
            ->orderBy('a.id', 'desc');


        return DataTables::of($query)

            ->addColumn('edit', function ($row) {
                return
                    '<a href="' . url('v1/schedule-shift/edit/' . $row->id) . '" class="btn btn-sm btn-primary">Edit</a>';
            })

            ->addColumn('delete', function ($row) {
                return '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">Delete</button>';
            })
            ->rawColumns(['edit', 'delete'])
            ->make(true);
    }

    public function add()
    {

        return view('pages.level.app.shift.form');
    }

    public function store(Request $request)
    {

        $companyId = $request->company_id;
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

            $shifts = DB::table('schedule_shift')
                // ->where('company_id', $companyId)
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
                // ->where('uti.branch_id', $branchId)
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


    public function edit($id)
    {
        $data = DB::table('schedule_shift as a')
            ->where('a.id', $id)
            ->select([
                'a.id',
                'a.code as code',
                'a.name',
                'a.start_time',
                'a.end_time',
                'a.created_at',
                'a.company_id'
            ])
            ->first();
        // dd($query);
        return view('pages.level.app.shift.form_edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);

        $startTime = $request->start_hour . ':' . $request->start_minute . ':00';
        $endTime   = $request->end_hour . ':' . $request->end_minute . ':00';

        DB::table('schedule_shift')->where('id', $id)->update([
            'code' => $request->code,
            'name' => $request->name,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'company_id' => $request->company_id,
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Shift berhasil diupdate');
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $shift = DB::table('schedule_shift')->where('id', $id)->first();

            if (!$shift) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Shift tidak ditemukan'
                ], 404);
            }

            DB::table('schedule_shift')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Shift berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
