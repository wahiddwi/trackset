<?php

namespace App\Models;

use App\Blameable;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Insurance extends Model
{
    use HasFactory, Blameable;

    protected $table = 'insurance';
    protected $fillable = [
        'vehicle_id',
        'polis_no', // no. polis
        'polis_expire', // jatuh tempo polis
        // 'polis_document' // png, jpg, pdf
    ];

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('id', '<>', 1);
        }
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }
}
