<?php

namespace App\Models;

use App\Models\RequestMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestDetail extends Model
{
    use HasFactory;

    protected $table = 'request_dtl';
    protected $fillable = [
        'reqdtl_id',
        'reqdtl_code',
        'reqdtl_item',
        'reqdtl_uom',
        'reqdtl_qty',
        'reqdtl_qty_approve',
        'reqdtl_qty_send',
        'reqdtl_qty_purchase',
        'reqdtl_line',
        // 'is_asset',
        'reqdtl_trfno',
    ];

    public function requestMaster()
    {
        return $this->belongsTo(RequestMaster::class, 'reqdtl_id', 'id');
    }
}
