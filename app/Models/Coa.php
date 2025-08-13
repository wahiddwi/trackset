<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    protected $table = 'coa';
    protected $primaryKey = 'coa_account';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'coa_name'
    ];

    public function category()
    {
        return $this->hasMany(Category::class, 'cat_account', 'coa_account');
    }
}
