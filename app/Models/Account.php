<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;

    protected $table = 'coa';
    protected $primaryKey = 'coa_account';
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'coa_account',
        'coa_name',
        'coa_desc',
    ];

    public function scopeActive($query)
    {
        $query->where('coa_status', true);
    }

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('coa_account', '<>', 1);
        }
    }

    public function category()
    {
        return $this->hasMany(Category::class, 'cat_account', 'coa_account');
    }
}
