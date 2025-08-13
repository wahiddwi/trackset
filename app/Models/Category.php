<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'cat_code',
        'cat_name',
        'cat_active',
        'cat_asset', // akun asset
        // 'cat_percent', // percent of depreciation each category
        'cat_depreciation', // id depreciation
        'cat_accumulate_depreciation', // akun akumulasi penyusutan
        'cat_depreciation_expense', //akun  beban penyusutan
        // 'cat_income', // Akun pendapatan
        // 'cat_disposal', // Akun Disposal
        'is_vehicle'
    ];

    public function scopeActive($query)
    {
        $query->where('cat_active', true);
    }

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('id', '<>', 1);
        }
    }

    public function generateCode()
    {
        return IdGenerator::generate(['table' => 'categories', 'field' => 'cat_code', 'length' => 7, 'prefix' =>'CAT-']);
    }

    public function account_asset()
    {
        return $this->belongsTo(Coa::class, 'cat_asset', 'coa_account');
    }

    public function account_accumulate_dep()
    {
        return $this->belongsTo(Coa::class, 'cat_accumulate_depreciation', 'coa_account');
    }

    public function account_dep_expense()
    {
        return $this->belongsTo(Coa::class, 'cat_depreciation_expense', 'coa_account');
    }

    // public function account_income()
    // {
    //     return $this->belongsTo(Coa::class, 'cat_income', 'coa_account');
    // }

    // public function account_disposal()
    // {
    //     return $this->belongsTo(Coa::class, 'cat_disposal', 'coa_account');
    // }

    public function depreciation()
    {
        return $this->belongsTo(CategoryDepreciation::class, 'cat_depreciation', 'id');
    }

    public function asset()
    {
        return $this->hasMany(Asset::class, 'inv_category', 'id');
    }
}
