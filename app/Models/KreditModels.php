<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KreditModels extends Model
{
    use HasFactory;
    protected $table      = 'transaction_kredit';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    public $timestamps    = true;
    protected $fillable   = [
        'jasa', 'parts', 'bahan', 'OPL', 'OPB',
        'kode_jasa', 'kode_parts', 'kode_bahan',
        'kode_opl', 'kode_opb', 'wo'
    ];
}
