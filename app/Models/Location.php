<?php

namespace App\Models;

use App\Blameable;
use App\Models\Asset;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Location extends Model
{
    use HasFactory, Blameable;

    protected $table = 'loc_mstr';
    // protected $primaryKey = 'loc_id';
    // public $incrementing = false;
    // protected $keyType = 'string';


    protected $fillable = [
        'loc_id',
        'loc_site',
        'loc_name',
        'loc_active',
    ];

    /**
     * Get the site that owns the Location
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'loc_site', 'si_site');
    }

    public function inventory()
    {
        return $this->hashMany(Asset::class, 'inv_location', 'id');
    }

    public function scopeActive($query){
        return $query->where('loc_active', true);
    }

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('id', '<>', 1);
        }
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'purchase_loc', 'id');
    }
}
