<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor_mstr';
    protected $fillable = [
      'vdr_code',
      'vdr_name',
      'vdr_telp',
      'vdr_addr',
      'vdr_desc',
      'vdr_status',
    ];

  public function scopeActive($query)
  {
      $query->where('vdr_status', true);
  }
}
