<?php

namespace App\Models;

use App\Blameable;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Disposal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DisposalDetail extends Model
{
    use HasFactory,Blameable;
    protected $table = 'disposal_detail';
    protected $fillable = [
        'disdtl_asset_transno', // asset transno
        'disdtl_dis_id',
        'disdtl_transdate', // Tanggal disposal
        'disdtl_asset_name',
        'disdtl_asset_site',
        'disdtl_order', // urutan barang
        'disdtl_status', // ONHAND, RSV, DISPOSAL
        'disdtl_desc',
        'created_by',
        'created_name',
        'approved_by',
        'approved_name',
        'updated_name',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'disdtl_asset_transno', 'inv_transno');
    }

    public function disposal()
    {
        return $this->belongsTo(Disposal::class, 'disdtl_dis_id', 'id');
    }

    public function site()
    {
      return $this->belongsTo(Site::class, 'disdtl_asset_site', 'si_site');
    }
}
