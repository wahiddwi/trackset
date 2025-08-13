<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestMaster extends Model
{
    use HasFactory, Blameable;

    protected $table = 'request_mstr';
    protected $fillable = [
      'req_spb',
      'req_transdate',
      'req_company',
      'req_site',
      'req_status',
      'req_line',
      // 'created_by',
      // 'updated_by',
      'approver_by',
      'creator_name',
      'approver_name',
    ];

    public function detail()
    {
        return $this->hasMany(RequestDetail::class, 'reqdtl_id', 'id');
    }

    public function site()
    {
      return $this->belongsTo(Site::class, 'req_site', 'si_site');
    }

    public function trf_detail()
    {
      return $this->hasMany(TransferDetail::class, 'trfdtl_spb', 'req_spb');
    }

    public function trf()
    {
      return $this->hasMany(Transfer::class, 'trf_spb', 'req_spb');
    }
}
