<?php

namespace App\Models;

use App\Blameable;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Receive;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiveDetail extends Model
{
    use HasFactory, Blameable;

    protected $table = 'transfer_detail';
    protected $fillable = [
        'rcv_id',
        'rcv_detail_id',
        'rcv_detail_transno',
        'rcv_detail_name'
    ];

    public function transfer()
    {
        return $this->belongsTo(Receive::class, 'rcv_id', 'id');
    }

    public function asset()
    {
        return $this->hasOne(Asset::class, 'inv_transno', 'rcv_detail_transno');
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $date = Carbon::now()->toArray();
            $currentDate = $date['day'] . ':' . $date['month'];
            $model->rcv_detail_id = IdGenerator::generate(['table' => 'receive_detail', 'field' => 'rcv_detail_id', 'length' => 15, 'prefix' => 'RDL-' . $currentDate . '-']);
        });
    }
}
