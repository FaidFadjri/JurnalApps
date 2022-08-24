<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebitModels extends Model
{
    use HasFactory;
    protected $table      = 'transaction_debit';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    public $timestamps    = true;
    protected $fillable   = [
        'jasa', 'parts', 'bahan', 'OPL', 'OPB',
        'kode_jasa', 'kode_parts', 'kode_bahan',
        'kode_opl', 'kode_opb', 'ppn', 'kode_ppn',
        'total', 'wo'
    ];
}
