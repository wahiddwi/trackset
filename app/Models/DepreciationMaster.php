<?php

namespace App\Models;

use App\Models\DepreciationDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepreciationMaster extends Model
{
    use HasFactory;

    protected $table = 'depreciation_mstr';
    protected $fillable = [
      'dep_company', // company code
      'dep_doc_ref', // no document reference (dep_transno)
      'dep_periode', // date periode Jan-24
      'dep_eff_date', // effective date 31-Jan-24
      'dep_status', // OPEN, DONE
    ];

    protected $dates = ['dep_eff_date', 'dep_periode'];

    public function scopeActive($query)
    {
        $query->where('dep_status', true);
    }

    public function scopeIsSuper($query)
    {
        if (Auth::user()->role_id == 1) {
            $query;
        } else {
            $query->where('id', '<>', 1);
        }
    }

    public function detail()
    {
        return $this->hasMany(DepreciationDetail::class, 'depdtl_doc_id', 'id');
    }

    public function company()
    {
      return $this->belongsTo(Company::class, 'dep_company', 'co_company');
    }
}
