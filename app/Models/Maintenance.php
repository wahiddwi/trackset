<?php

namespace App\Models;

use App\Blameable;
use App\Models\Vendor;
use App\Models\MaintenanceDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Maintenance extends Model
{
    use HasFactory, Blameable;
    
    protected $table = 'maintenance_mstr';
    protected $fillable = [
      'main_transno',
      'main_transdate',
      'main_company',
      'main_vendor',
      'main_status',
      'asset_count',
      'main_total_cost',
      'created_by_name',
      'updated_by_name',
      'approver_by',
      'approver_by_name',
    ];

    protected $dates = ['main_transdate'];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'main_asset_id', 'id');
    }

    public function history()
    {
        return $this->hasMany(MaintenanceHist::class, 'mainhist_main_id', 'id');
    }

    public function detail()
    {
      return $this->hasMany(MaintenanceDetail::class, 'maindtl_id', 'id');
    }

    public function company()
    {
      return $this->belongsTo(Company::class, 'main_company', 'co_company');
    }

    public function vendor()
    {
      return $this->belongsTo(Vendor::class, 'main_vendor', 'id');
    }
}
