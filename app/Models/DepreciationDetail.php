<?php

namespace App\Models;

use App\Models\DepreciationMaster;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepreciationDetail extends Model
{
    use HasFactory;
    protected $table = 'depreciation_dtl';
    protected $fillable = [
      // 'dtldep_doc_ref', // no doc ref dep_mstr
      'depdtl_doc_id', // id doc ref dep_mstr
      'depdtl_asset_transno', // asset transno
      'depdtl_company', // company
      'depdtl_site', // site
      'depdtl_category', // category
      'depdtl_acc_accumulate_dep', // account accumulate depreciation
      'depdtl_acc_expense_dep', // account expense depreciation
      // 'depdtl_acc_fixed_asset', // account fixed asset
      'depdtl_asset_price', // Harga Asset
      'depdtl_nominal_dep', // Nominal depreciation
      'depdtl_accumulate_dep', // Akumulasi Depresiasi
      'depdtl_current_price', // Harga setelah depresiasi
      'depdtl_dep_amount', // jumlah depresiasi
      'depdtl_desc', // deskripsi
      'depdtl_doc_ref', // doc_ref
    ];

    public function depre_mstr()
    {
      return $this->belongsTo(DepreciationMaster::class, 'depdtl_doc_id', 'id');
    }
}
