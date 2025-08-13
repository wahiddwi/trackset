<?php

namespace App\Models;

use App\Models\Asset;
use App\Models\Selling;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellingDetail extends Model
{
    use HasFactory;

    protected $table = 'sell_detail';
    protected $fillable = [
        'selldtl_asset_id', // ID ASSET
        'selldtl_id', // ID SELL
        'selldtl_transno', // INV_TRANSNO
        'selldtl_transdate',
        'selldtl_asset_name',
        'selldtl_acc_dep', // akumulasi depresiasi
        'selldtl_dep_price', // depresiasi price
        'selldtl_price',
        'selldtl_status',
        'selldtl_desc',
        'selldtl_order'

    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'selldtl_asset_id', 'id');
    }

    public function selling()
    {
        return $this->belongsTo(Selling::class, 'selldtl_id', 'id');
    }
}
