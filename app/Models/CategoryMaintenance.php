<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMaintenance extends Model
{
    use HasFactory;

    protected $table = 'category_maintenance';
    protected $fillable = [
      'mtn_type', 'mtn_desc', 'mtn_status'
    ];

    public function scopeActive($query)
    {
      $query->where('mtn_status', true);
    }
}
