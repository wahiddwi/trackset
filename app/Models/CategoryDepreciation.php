<?php

namespace App\Models;

use App\Models\Category;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CategoryDepreciation extends Model
{
    use HasFactory;

    protected $table = 'category_depreciations';

    protected $fillable = [
        'dep_code',
        'dep_periode',
        'dep_type',
        'dep_amount_periode',
        'dep_active',
    ];

    public function scopeActive($query)
    {
        $query->where('dep_active', true);
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
        return IdGenerator::generate(['table' => 'category_depreciations', 'field' => 'dep_code', 'length' => 7, 'prefix' =>'DEP-']);
    }

    public function category()
    {
        return $this->hasMany(Category::class, 'cat_depreciation', 'id');
    }

    // public function purchaseDetail()
    // {
    //     // return $this->belongsTo(PurchaseDetail::class, '');
    // }
}
