<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCode extends Model
{
    use HasFactory;
    protected $table    = 'transaction_code';
    protected $fillable = ['code', 'keterangan'];
    protected $primaryKey = 'code';
}
