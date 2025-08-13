<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Blameable;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class Role extends SpatieRole
{
    use HasFactory, Blameable;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'role_name',
        'role_active',
        'guard_name'
    ];

    public function scopeActive($query)
    {
        $query->where('role_active', true);
    }

    public function scopeIsSuper($query)
    {
        if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
            $query;
        }
        else{
            $query->where('id', '<>', 1);
        }
    }

    public function generateCode()
    {
        return IdGenerator::generate(['table' => 'roles', 'field' => 'name', 'length' => 7, 'prefix' =>'POS-']);
    }
}
