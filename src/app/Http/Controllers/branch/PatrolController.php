<?php

namespace App\Http\Controllers\branch;

use App\Http\Controllers\Controller;
use App\Models\MasterPatroli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class PatrolController extends Controller
{
    public function index()
    {
        return view('pages.level.branch.patrol.index');
    }

    public function add()
    {
        return view('pages.level.branch.patrol.form');
    }

    public function store(Request $request)
    {
        $userBranch = DB::table('user_branch')
            ->where('user_id', Auth::user()->id)
            ->first();

        $branch = DB::table('branch')
            ->where('id', $userBranch->branch_id)
            ->first();

        $companyId = $branch->company_id;
        $branchId  = $branch->id;


        $request->validate([
            'kode' => 'required|unique:master_patroli,kode',
        ]);

        $payload = json_encode([
            'patroli_id' => null, // placeholder
            'branch_id' => $branchId
        ]);

        $patroli = MasterPatroli::create([
            'kode' => $request->kode,
            'branch_id' => $branchId,
            'nama_lokasi' => $request->nama_lokasi,
            'barcode_payload' => $payload
        ]);

        // update payload dengan id
        $payload = json_encode([
            'patroli_id' => $patroli->id,
            'branch_id'  => $patroli->branch_id,
        ]);

        $qrCode = new QrCode(
            data: $payload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $path = "patroli/qr_{$patroli->id}.png";

        Storage::disk('public')->put($path, $result->getString());

        $patroli->update([
            'barcode_path' => $path
        ]);

        return redirect()->back()->with('success', 'Master patroli berhasil dibuat');
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

        $query = MasterPatroli::where('branch_id', $branchId)
            ->select([
                'id',
                'kode',
                'nama_lokasi',
                'branch_id',
                'barcode_path',
                'is_active'
            ]);

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('qr', function ($row) {
                return $row->barcode_path
                    ? '<img src="' . asset('storage/' . $row->barcode_path) . '" width="70">'
                    : '-';
            })

            ->addColumn('status', function ($row) {
                return $row->is_active
                    ? '<span class="badge bg-success">Aktif</span>'
                    : '<span class="badge bg-danger">Nonaktif</span>';
            })

            ->addColumn('action', function ($row) {
                return '<a href="' . '#' . '"
                        class="btn btn-sm btn-primary">
                        Download
                    </a>';
            })

            ->rawColumns(['qr', 'status', 'action'])
            ->make(true);
    }
}
