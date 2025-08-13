<?php

namespace App\Models;

use App\Blameable;
use Carbon\Carbon;
use App\Models\Purchase;
use App\Models\CategoryDepreciation as Depreciation;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseDetail extends Model
{
    use HasFactory, Blameable;

    protected $table = 'purchase_detail';

    protected $fillable = [
        'purchase_detail_id',
        'purchase_code', // purchase id
        'purchase_detail_name',
        'purchase_detail_type', // category
        'purchase_detail_dep', // category deprecation
        'purchase_detail_price',
        'purchase_detail_ref',
        'purchase_detail_status', // on hand or transit
        'purchase_detail_account' // coa account

    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_code', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'purchase_detail_type', 'id');
    }

    public function user()
    {
        return $this->belongsto(User::class, 'created_by', 'usr_nik');
    }

    public function depreciation()
    {
        return $this->belongsTo(Depreciation::class, 'purchase_detail_dep', 'id');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $date = Carbon::now()->toArray();
            $currentDate = $date['day'] . ':' . $date['month'];
            $model->purchase_detail_id = IdGenerator::generate(['table' => 'purchase_detail', 'field' => 'purchase_detail_id', 'length' => 15, 'prefix' => 'AST-' . $currentDate . '-']);
        });
    }
}
