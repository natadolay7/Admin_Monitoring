<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPatroli extends Model
{
    protected $table = 'master_patroli';

    protected $fillable = [
        'kode',
        'branch_id',
        'nama_lokasi',
        'barcode_payload',
        'barcode_path',
        'is_active'
    ];
}
