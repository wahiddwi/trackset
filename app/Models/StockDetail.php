<?php

namespace App\Models;

use App\Models\Pic;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Location;
use App\Models\StockMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockDetail extends Model
{
    use HasFactory;

    protected $table = 'stock_dtl';
    protected $fillable = [
      'stockdtl_transno',
      'stockdtl_trn_transno',
      'stockdtl_desc',
      'stockdtl_note', // keterangan opname
      'stockdtl_status', // OPNAME, FOUND
      'stockdtl_site',
      'stockdtl_loc',
      'stockdtl_name',
      'stockdtl_pic',
      'stockdtl_pic_name',
      'stockdtl_obtaindate',
      'stockdtl_price',
      'stockdtl_current_price',
      'stockdtl_type', // ADDITIONAL, ITEM
      'stockdtl_order', // ordering
    ];

    protected $dates = ['stockdtl_obtaindate'];

    public function stock()
    {
      return $this->belongsTo(StockMaster::class, 'stockdtl_transno', 'stock_transno');
    }

    public function site()
    {
      return $this->belongsTo(Site::class, 'stockdtl_site', 'si_site');
    }

    public function loc()
    {
      return $this->belongsTo(Location::class, 'stockdtl_loc', 'id');
    }

    public function asset()
    {
      return $this->belongsTo(Asset::class, 'stockdtl_trn_transno', 'inv_transno');
    }

    public function pic()
    {
      return $this->belongsTo(Pic::class, 'stockdtl_pic', 'pic_nik');
    }

    public function pic_site()
    {
      return $this->belongsTo(Site::class, 'stockdtl_pic', 'si_site');
    }
}
