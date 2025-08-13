<?php

namespace App\Models;

use App\Blameable;
use App\Models\Asset;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory, Blameable;

    protected $table = 'sites';
    protected $primaryKey = 'si_site';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'si_site',
        'si_name',
        'si_company',
        'si_company_site',
        'si_active',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'si_company', 'co_company');
    }

    public function scopeActive($query)
    {
        $query->where('si_active', true);
    }

    public function scopeHeadOffice($query)
    {
        $query->where('si_company_site', true)->orderBy('si_site', 'asc');
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'purchase_site', 'si_site');
    }

    public function asset()
    {
        return $this->hasMany(Asset::class, 'inv_site', 'si_site');
    }

    public function loc()
    {
      return $this->hasMany(Location::class, 'loc_site', 'si_site');
    }
}
