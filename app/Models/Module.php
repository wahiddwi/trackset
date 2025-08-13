<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory, \App\Blameable;

    protected $keyType = 'string';
    protected $primaryKey = 'mod_id';
    protected $fillable = [
        'mod_code',
        'mod_name',
        'mod_path',
        'mod_desc',
        'mod_icon',
        'mod_parent',
        'mod_active',
        'mod_order',
        'mod_superuser'
    ];

    public function parent(){
        return $this->belongsTo(Module::class, 'mod_parent', 'mod_id');
    }

    public function children() {
        return $this->hasMany(Module::class,'mod_parent','mod_id')->isSuper()->active();
    }

    public function submenu(){
        return $this->children()->with('submenu')->orderBy('mod_order', 'asc');
    }

    public function scopeIsSuper($query)
    {
        if(\Illuminate\Support\Facades\Auth::user()->role_id == 1){
            $query;
        }
        else{
            $query->where('mod_superuser', '<>', 1);
        }
    }

    public function scopeActive($query)
    {
        $query->where('mod_active', true);
    }
}
