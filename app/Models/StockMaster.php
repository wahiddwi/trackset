<?php

namespace App\Models;

use App\Models\Site;
use App\Models\Location;
use App\Models\StockDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMaster extends Model
{
    use HasFactory;

    protected $table = 'stock_mstr';
    protected $fillable = [
      'stock_transno',
      'stock_transdate',
      'stock_desc',
      'stock_status', // OPEN, CLOSE
      'stock_site',
      'stock_site_name',
      'stock_loc',
      'stock_loc_name',
      'stock_itemttl',
      'stock_found', // counting item found
      'stock_opname', // counting item opname
      'stock_counter',
      'stock_additional', // counting item additional
    ];

    protected $dates = ['stock_transdate'];

    public function stock_detail()
    {
      return $this->hasMany(StockDetail::class, 'stockdtl_transno', 'stock_transno');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'stock_site', 'si_site');
    }

    public function loc()
    {
        return $this->belongsTo(Location::class, 'stock_loc', 'id');
    }
}
