<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceHist extends Model
{
    use HasFactory;

    protected $table = 'insurance_hist';
    protected $fillable = [
      'inshist_transno',
      // 'inshist_name',
      'inshist_asset',
      'inshist_vendor',
      'inshist_vehicle',
      'inshist_polishno',
      'inshist_startdate',
      'inshist_enddate',
      'inshist_cover', // nilai pertanggungan
      'inshist_premi',
    ];

    public function asset()
    {
      return $this->belongsTo(Asset::class, 'inshist_asset', 'id');
    }

    public function vendor()
    {
      return $this->belongsTo(Vendor::class, 'inshist_vendor', 'id');
    }

    public function vehicle()
    {
      return $this->belongsTo(Vehicle::class, 'inshist_vehicle', 'id');
    }
}
