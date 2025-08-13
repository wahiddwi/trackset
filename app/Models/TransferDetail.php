<?php

namespace App\Models;

use App\Blameable;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Transfer;
use App\Models\RequestMaster;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransferDetail extends Model
{
    use HasFactory, Blameable;

    protected $table = 'transfer_detail';
    protected $fillable = [
        'trfdtl_id', // trf_id
        'rcv_id', // receive id
        'trfdtl_transno', // trf detail transno
        'trfdtl_name', // trf detail name
        'trfdtl_status', // status receive 
        'trfdtl_pic', // user approve
        'trfdtl_transdate',
        'trfdtl_company',
        'trfdtl_site_from',
        'trfdtl_loc_from',
        'trfdtl_pic_type_from',
        'trfdtl_pic_from',
        'trfdtl_site_to',
        'trfdtl_loc_to',
        'trfdtl_pic_type_to',
        'trfdtl_pic_to',
        'trfdtl_desc', 
        'trfdtl_order',
        'trfdtl_received_by', // user yg terima barang
        'trfdtl_created_name',
        'trfdtl_updated_name',
        'trfdtl_approver_nik',
        'trfdtl_approver_name',
        'trfdtl_itemcode',
        'trfdtl_spb',

    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'trfdtl_id', 'id');
    }

    public function asset()
    {
        return $this->hasOne(Asset::class, 'inv_transno', 'trfdtl_transno');
    }

    public function received()
    {
        return $this->belongsTo(User::class, 'trfdtl_received_by', 'usr_nik');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'usr_nik');
    }

    public function pic_to()
    {
      return $this->belongsTo(Pic::class, 'trfdtl_pic_to', 'pic_nik');
    }

    public function site_to()
    {
      return $this->belongsTo(Site::class, 'trfdtl_site_to', 'si_site');
    }

    public function req()
    {
      return $this->belongsTo(RequestMaster::class, 'trfdtl_spb', 'req_spb');
    }
}
