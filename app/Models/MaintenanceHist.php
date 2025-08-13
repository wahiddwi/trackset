<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceHist extends Model
{
    use HasFactory, Blameable;
    protected $table = 'maintenance_hist';
    protected $fillable = [
      'mainhist_main_id',
      'mainhist_transdate',
      'mainhist_asset_id',
      'mainhist_asset_transno',
      'mainhist_asset_name',
      'mainhist_company',
      'mainhist_site',
      'mainhist_vendor',
      'mainhist_cost',
      'mainhist_desc',
      'mainhist_count',
      'created_by_name',
      'updated_by_name',
      'approver_by',
      'approver_by_name',
      'mainhist_cat_mtn',
      'mainhist_lastdate',
      'mainhist_mileage'
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'mainhist_main_id', 'id');
    }
}
