<?php

namespace App\Models;

use App\Blameable;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvHist extends Model
{
    use HasFactory, Blameable;

    protected $table = 'inv_hist';

    protected $fillable = [
        'invhist_transno', // generate id asset dibuat setelah approve
        'invhist_inv', // id asset
        'invhist_category',
        'invhist_site',
        'invhist_loc',
        'invhist_depreciation',
        'invhist_name',
        'invhist_pic',
        'invhist_obtaindate', // tgl perolehan
        'invhist_price',
        'invhist_status', // ONHAND, TRF
        'invhist_desc',
        'invhist_sn', // imei or Serial Number
        'invhist_doc_ref', // document referensi
        'invhist_merk', // merk / brand of product
        'invhist_cur_price', // harga setelah di depresiasi
        'invhist_dep_periode', // total  depresiasi (bulan)
        'invhist_dep_amount', // jumlah berapa kali terdepresiasi
        'invhist_company', // PT
        'invhist_tag',
        'invhist_name_short',
        'is_vehicle',
    ];

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the site that owns the InvHist
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'invhist_site', 'si_site');
    }

    /**
     * Get the location that owns the InvHist
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'invhist_loc', 'id');
    }

    public function inventory()
    {
        return $this->belongsTo(Asset::class, 'id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'invhist_category', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'invhist_pic', 'usr_nik');
    }
}
