<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralParam extends Model
{
    use HasFactory;

    protected $table = 'general_param';
    protected $fillable = [
      'param_sales_profit', // laba penjualan asset tetap
      'param_sales_loss', // rugi penjualan asset tetap
      'param_expense_loss', // rugi beban write off
      'param_asset_transaction', // transaksi aktiva tetap
      'param_cash', // kas,
    ];
}
