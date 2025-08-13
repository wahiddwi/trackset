<?php

namespace App\Models;

use App\Blameable;
use App\Models\Site;
use App\Models\User;
use App\Models\Location;
use App\Models\ReceiveDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receive extends Model
{
    use HasFactory, Blameable;

    protected $table = 'transfer';
    protected $fillable = [
        'rcv_id',
        'rcv_site_from',
        'rcv_site_to',
        'rcv_loc_from',
        'rcv_loc_to',
        'pic_type_from',
        'pic_type_to',
        'rcv_pic_from',
        'rcv_pic_to',
        'rcv_status' // DRAFT, POST
    ];

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
        return $this->belongsTo(Site::class, 'rcv_site_from', 'si_site');
    }

    public function locFrom()
    {
        return $this->belongsTo(Location::class, 'rsv_loc_from', 'id');
    }

    public function siteTo()
    {
        return $this->belongsTo(Site::class, 'rcv_site_to', 'si_site');
    }

    public function locTo()
    {
        return $this->belongsTo(Location::class, 'rcv_loc_to', 'id');
    }

    public function detail()
    {
        return $this->hasMany(ReceiveDetail::class, 'receive_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'usr_nik', 'rcv_pic_to');
    }
}
