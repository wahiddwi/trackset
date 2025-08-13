<?php

namespace App\Models;

use App\Models\Site;
use App\Models\Company;
use App\Models\Customer;
use App\Models\SellingDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Selling extends Model
{
    use HasFactory;

    protected $table = 'sell_mstr';

    protected $fillable = [
        'sell_no', // custom ID selling
        'sell_transdate', // tgl. transaction
        'sell_company', // PT. yg menjual
        'sell_site', // Cabang yg menjual
        'sell_desc', // description
        'sell_cust_id', // ID Customer
        'sell_cust_name', // CUst Name
        'sell_cust_no', // Cust No Identity
        'sell_cust_addr', // Cust Address
        'sell_cust_telp',
        'sell_cust_wa',
        'sell_cust_email',
        'sell_status', // RESERVE(RSV), SELL
        'sell_qty_item', // count item
        'sell_total_price', // amount of sell price
        'sell_amount_dep_price', // amount of depreciation price
        'sell_created_name', // created name
        'sell_created_nik', // created_nik
        'sell_approver_name', // approver name
        'sell_approver_nik', // approver nik
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'sell_company', 'co_company');
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'sell_site', 'si_site');
    }

    public function customer()
    {
      return $this->belongsTo(Customer::class, 'sell_cust_id', 'id');
    }

    public function detail()
    {
      return $this->hasMany(SellingDetail::class, 'selldtl_id', 'id');
    }

    public function scopeIsSuper($query)
    {
      if (Auth::user()->role_id == 1) {
        $query;
      } else {
        $query->where('id', '<>', 1);
      }
    }
}
