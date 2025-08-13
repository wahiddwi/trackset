<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'brand_mstr';
    protected $fillable = [
      'brand_name', 'brand_status'
    ];

    public function scopeActive($query)
    {
      $query->where('brand_status', true);
    }

    public function asset()
    {
      return $this->hasMany(Asset::class, 'inv_merk', 'id');
    }
}
