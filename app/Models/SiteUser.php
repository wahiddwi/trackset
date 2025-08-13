<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteUser extends Model
{
    use HasFactory;

    protected $table = 'site_user';
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'su_user',
        'su_site',
        'su_default'
    ];

    public $timestamps = false;

    public function scopeDefault($query)
    {
        $query->where('su_default', true)->first();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usr_id', 'su_user');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'su_site', 'si_site');
    }
}
