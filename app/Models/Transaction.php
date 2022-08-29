<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transaction_detail';
    protected $fillable = [
        'jasa',
        'parts',
        'bahan',
        'OPL',
        'OPB',
        'kode_jasa',
        'kode_parts',
        'kode_bahan',
        'kode_opl',
        'kode_opb',

        'discJasa',
        'discParts',
        'discBahan',
        'discOPL',
        'discOPB',
        'kode_discJasa',
        'kode_discParts',
        'kode_discBahan',
        'kode_discOpl',
        'kode_discOpb',
        'ppn',
        'kode_ppn',
        'total',
        'kode_total',

        'id_pkb',
        'invoice_date',
    ];
}
