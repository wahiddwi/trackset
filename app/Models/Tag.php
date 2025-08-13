<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tag_mstr';

    protected $fillable = ['tag_name', 'tag_status'];

    public function scopeActive($query)
    {
      $query->where('tag_status', true);
    }

    public function asset()
    {
      return $this->hasMany(Asset::class, 'inv_tag', 'id');
    }
}
