<?php

namespace App\Models;

use App\Blameable;
use App\Models\Pic;
use App\Models\Site;
use App\Models\User;
use App\Models\Company;
use App\Models\Location;
use App\Models\TransferDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransferDetail as Detail;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transfer extends Model
{
    use HasFactory, Blameable;

    protected $table = 'transfer';
    protected $fillable = [
        'trf_transno', // trf transno
        'trf_company', // company
        'trf_transdate', // tgl transfer
        'trf_site_from',
        'trf_loc_from',
        'trf_pic_type_from',
        'trf_pic_from',
        'trf_site_to',
        'trf_loc_to',
        'trf_pic_type_to',
        'trf_pic_to',
        'trf_status', // DRAFT, TRF, ONHAND, CANCEL or TRF, ONHAND, CANCEL
        'trf_desc',
        'trf_count',
        'trf_created_name',
        'trf_updated_name',
        'trf_approver_nik',
        'trf_approver_name',
        'trf_spb'
    ];

    protected $dates = ['trf_transdate'];

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('id', '<>', 1);
        }
    }

    public function siteFrom()
    {
        return $this->belongsTo(Site::class, 'trf_site_from', 'si_site');
    }

    public function locFrom()
    {
        return $this->belongsTo(Location::class, 'trf_loc_from', 'id');
    }

    public function siteTo()
    {
        return $this->belongsTo(Site::class, 'trf_site_to', 'si_site');
    }

    public function locTo()
    {
        return $this->belongsTo(Location::class, 'trf_loc_to', 'id');
    }

    public function detail()
    {
        return $this->hasMany(TransferDetail::class, 'trfdtl_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usr_nik', 'trf_pic_to');
    }

    public function userFrom()
    {
        return $this->belongsTo(Pic::class, 'trf_pic_from', 'pic_nik');
    }

    public function userTo()
    {
        return $this->belongsTo(Pic::class, 'trf_pic_to', 'pic_nik');
    }

    public function company()
    {
      return $this->belongsTo(Company::class, 'trf_company', 'co_company');
    }

    public function req()
    {
      return $this->belongsTo(RequestMaster::class, 'trf_spb', 'req_spb');
    }

}
