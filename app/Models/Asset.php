<?php

namespace App\Models;

use App\Blameable;
use App\Models\Pic;
use App\Models\Tag;
use App\Models\Site;
use App\Models\User;
use App\Models\Brand;
use App\Models\InvHist;
use App\Models\Vehicle;
use App\Models\Category;
use App\Models\Location;
use App\Models\InsuranceHist;
use App\Models\TransferDetail;
use App\Models\DepreciationHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory, Blameable;

    protected $table = 'inv_mstr';

    protected $fillable = [
        'inv_transno',
        'inv_obtaindate',
        'inv_site',
        'inv_loc',
        'inv_pic_type',
        'inv_pic',
        'inv_name',
        'inv_category',
        'inv_price', // harga awal
        'inv_depreciation',
        'inv_status', // DRAFT, ONHAND, RSV, TRF, CANCEL
        'inv_desc',
        'inv_sn',
        'inv_doc_ref',
        'inv_merk', // merk / brand of asset
        'inv_current_price', // harga setelah di depresiasi
        'inv_dep_periode', // total  depresiasi (bulan)
        'inv_dep_amount', // jumlah berapa kali terdepresiasi
        'inv_company', // PT
        'inv_tag',
        'inv_name_short',
        'is_vehicle',

        'inv_accumulate_dep', // akumulasi penyusutan
        'inv_nominal_dep', // nominal penyusutan
        'inv_end_date', // tanggal akhir bulan
        'inv_last_periode', // last depresiasi Jan-2024
    ];

    protected $dates = ['inv_obtaindate', 'inv_end_date', 'inv_last_periode', 'created_at', 'updated_at'];

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('id', '<>', 1);
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'inv_category', 'id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'inv_site', 'si_site');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'inv_loc', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'inv_pic', 'usr_nik');
    }

    public function pic()
    {
      return $this->belongsTo(Pic::class, 'inv_pic', 'pic_nik');
    }

    public function history()
    {
        return $this->hasMany(InvHist::class, 'invhist_inv', 'id');
    }

    public function transferDetail()
    {
        return $this->belongsTo(TransferDetail::class, 'inv_transno', 'trf_detail_transno');
    }

    public function depreciationHistory()
    {
        return $this->hasMany(DepreciationHistory::class, 'dephist_asset_id', 'inv_transno');
    }

    public function tag()
    {
      return $this->belongsTo(Tag::class, 'inv_tag', 'id');
    }

    public function merk()
    {
      return $this->belongsTo(Brand::class, 'inv_merk', 'id');
    }

    public function vehicle()
    {
      return $this->hasOne(Vehicle::class, 'vehicle_transno', 'inv_transno');
    }

    public function insurance()
    {
      return $this->hasOne(InsuranceHist::class, 'inshist_asset', 'id');
    }
}
