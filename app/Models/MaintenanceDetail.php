<?php

namespace App\Models;

use App\Blameable;
use App\Models\Maintenance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceDetail extends Model
{
    use HasFactory, Blameable;
    protected $table = 'maintenance_dtl';
    protected $fillable = [
      'maindtl_id',
      'maindtl_transdate',
      'maindtl_asset_id',
      'maindtl_asset_transno',
      'maindtl_asset_name',
      'maindtl_company',
      'maindtl_site',
      'maindtl_vendor',
      'maindtl_cost',
      'maindtl_desc',
      'maindtl_status',
      'maindtl_line',
      'maindtl_count',
      'created_by_name',
      'updated_by_name',
      'approver_by',
      'approver_by_name',
      'maindtl_cat_mtn',
      'maindtl_lastdate',
      'maindtl_mileage'
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maindtl_id', 'id');
    }
}
