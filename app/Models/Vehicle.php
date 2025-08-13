<?php

namespace App\Models;

use App\Blameable;
use App\Models\User;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Insurance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicle_mstr';
    protected $fillable = [
        'vehicle_no', // no. Kendaraan
        'vehicle_transno', // no. asset
        'vehicle_brand', // merk
        'vehicle_name',
        'vehicle_identityno', // no. rangka
        'vehicle_engineno', // no. mesin
        'vehicle_color',
        'is_insurance', // tercover asuransi ?
        'vehicle_documentno', // no. stnk
        'vehicle_capacity', // besaran CC / daya
        // 'vehicle_expire', // masa berlaku pajak tahunan
        // 'vehicle_expire_end', // masa berlaku pajak 5 tahun
        'vehicle_desc', // description
        'vehicle_status',
        'created_by_name',
        'updated_by_name',
        'vehicle_last_km' // last kilometer
    ];

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('id', '<>', 1);
        }
    }

    public function scopeActive($query)
    {
        $query->where('vehicle_status', true);
    }

    public function asset()
    {
      return $this->belongsTo(Asset::class, 'vehicle_transno', 'inv_transno');
    }

    // public function category()
    // {
    //     return $this->belongsTo(Category::class, 'category_id', 'id');
    // }

    // public function insurance()
    // {
    //     return $this->hasOne(Insurance::class, 'vehicle_id', 'id');
    // }
    
    public function user()
    {
      return $this->belongsTo(User::class, 'usr_nik', 'created_by');
    }

    public function brand()
    {
      return $this->belongsTo(Brand::class, 'vehicle_brand', 'id');
    }

    public function insurance()
    {
      return $this->hasOne(InsuranceHist::class, 'inshist_vehicle', 'id');
    }
}
