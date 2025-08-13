<?php

namespace App\Models;

use App\Blameable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, Blameable;

    protected $table = 'companies';
    protected $primaryKey = 'co_company';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'co_company',
        'co_name',
        'co_active',
        'last_dep', // date last depreciation
    ];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class, 'si_company', 'co_company');
    }

    public function scopeActive($query)
    {
        $query->where('co_active', true);
    }
}
