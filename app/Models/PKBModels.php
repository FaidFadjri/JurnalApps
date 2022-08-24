<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PKBModels extends Model
{
    use HasFactory;
    protected $table      = 'transaction_pkb';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    public $timestamps    = true;
    protected $fillable   = ['wo', 'license_plate', 'customer', 'invoice_date', 'created_at', 'updated_at'];
}
