<?php

namespace App\Models;

use App\Blameable;
use App\Models\Transfer;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Blameable, HasRoles;

    protected $table = 'users';

    protected $primaryKey = 'usr_id';
    protected $fillable = [
        'usr_nik',
        'usr_name',
        'password',
        'role_id',
        'usr_status',
        'usr_email'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    public function site_privileges(): HasMany
    {
        return $this->hasMany(SiteUser::class, 'su_user', 'usr_id');
    }

    public function scopeActive($query)
    {
        $query->where('usr_status', true);
    }

    public function scopeIsSuper($query)
    {
        if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
            $query;
        }
        else{
            $query->where('role_id', '<>', 1);
        }
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'purchase_pic', 'usr_nik');
    }

    public function author()
    {
        return $this->hasMany(Purchase::class, 'created_by', 'usr_nik');
    }

    public function transferFrom()
    {
        return $this->hasMany(Transfer::class, 'trf_pic_from', 'usr_nik');
    }

    public function transferTo()
    {
        return $this->hasMany(Transfer::class, 'trf_pic_to', 'usr_nik');
    }
}
