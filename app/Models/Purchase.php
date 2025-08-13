<?php

namespace App\Models;

use App\Blameable;
use App\Models\Site;
use App\Models\User;
use App\Models\Location;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory, Blameable;

    protected $table = 'purchase';

    protected $fillable = [
        'purchase_id', // purchase no
        'purchase_date',
        'purchase_site',
        'purchase_loc',
        'pic_type', // user or cabang
        'purchase_pic',
        'purchase_status', // draft or posting
        'purchase_desc'
    ];

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('id', '<>', 1);
        }
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'purchase_site', 'si_site');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'purchase_loc', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'purchase_pic', 'usr_nik');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by', 'usr_nik');
    }

    public function detail()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_code', 'id');
    }
}
