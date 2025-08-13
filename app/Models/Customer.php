<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'cust_mstr';
    protected $fillable = [
      'cust_no', 'cust_type', 'cust_name', 'cust_addr', 'cust_telp', 'cust_wa', 'cust_email', 'cust_internal', 'cust_active', 'cust_status'
    ];

    public function scopeActive($query)
    {
      $query->where('cust_active', true);
    }

    public function scopeIsSuper($query)
    {
      if (Auth::user()->role_id == 1) {
        $query;
      } else {
        $query->where('id', '<>', 1);
      }
    }
}
