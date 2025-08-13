<?php

namespace App\Models;

use App\Blameable;
use App\Models\Site;
use App\Models\company;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disposal extends Model
{
    use HasFactory, Blameable;
    protected $table = 'disposal_mstr';
    protected $fillable = [
        'dis_transno', // custom Id disposal
        'dis_transdate', // tgl document, tgl transaksi
        'dis_company',
        // 'dis_site',
        // 'dis_loc',
        'dis_status', // ONHAND, RSV, DISPOSAL
        'dis_desc',
        'created_by', // nik kreator
        'approved_by',
        'approved_name',
        'created_name',
        'updated_name',
    ];

    protected $dates = ['dis_transdate'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'dis_company', 'co_company');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'dis_site', 'si_site');
    }
    // public function loc()
    // {
    //   return $this->belongsTo(Location::class, 'dis_loc', 'id');
    // }

    public function detail()
    {
      return $this->hasMany(DisposalDetail::class, 'disdtl_dis_id', 'id');
    }
}
