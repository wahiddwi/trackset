<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    use HasFactory;
    
    protected $table = 'pic';
    protected $fillable = ['pic_nik', 'pic_name'];

    public function scopeActive($query)
    {
        $query->where('pic_status', true);
    }
}
