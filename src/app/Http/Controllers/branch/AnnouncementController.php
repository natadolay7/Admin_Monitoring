<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AnnouncementController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.announcement.index');
    }

    public function datatable()
    {
        $branchId = getUserBranchId();
        $query = DB::table('announcements')
            ->where('branch_id', $branchId)
            ->select('id', 'title', 'start_date', 'end_date', 'status');



        return DataTables::of($query)
            ->addColumn('action', function ($row) {

                $btn = '';

                if (checkPermission('edit')) {
                    $btn .= '<a href="' . url('/master-pengumuman/edit/' . $row->id) . '"
                            class="btn btn-sm btn-primary me-1">
                            Edit
                         </a>';
                }

                if (checkPermission('delete')) {
                    $btn .= '<button class="btn btn-sm btn-danger btn-delete"
                            data-id="' . $row->id . '">
                            Delete
                         </button>';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public  function add()
    {
        if (checkPermission('add')) {
            return view('pages.level.branch.announcement.form');
        } else {
            echo "tidak punya akses";
        }
    }

    public function store(Request $request)
    {
        $branchId = getUserBranchId();
        // VALIDASI
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();

        try {
            DB::table('announcements')->insert([
                'title'      => $request->title,
                'content'    => $request->content,
                'start_date' => $request->start_date,
                'end_date'   => $request->end_date,
                'status'     => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
                'branch_id' => $branchId
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Pengumuman berhasil ditambahkan');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        if (!checkPermission('edit')) {
            abort(403, 'Tidak punya akses');
        }

        $branchId = getUserBranchId();

        $data = DB::table('announcements')
            ->where('id', $id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$data) {
            abort(404);
        }

        return view('pages.level.branch.announcement.form', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $branchId = getUserBranchId();

        $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:active,inactive',
        ]);

        DB::beginTransaction();

        try {
            DB::table('announcements')
                ->where('id', $id)
                ->where('branch_id', $branchId)
                ->update([
                    'title'      => $request->title,
                    'content'    => $request->content,
                    'start_date' => $request->start_date,
                    'end_date'   => $request->end_date,
                    'status'     => $request->status,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return redirect()
                ->to('/master-pengumuman')
                ->with('success', 'Pengumuman berhasil diupdate');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function delete($id)
    {
        if (!checkPermission('delete')) {
            return response()->json(['message' => 'Tidak punya akses'], 403);
        }

        $branchId = getUserBranchId();

        DB::table('announcements')
            ->where('id', $id)
            ->where('branch_id', $branchId)
            ->delete();

        return response()->json(['message' => 'Pengumuman berhasil dihapus']);
    }
}
