<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalLogs extends Model
{
    use HasFactory;

    protected $table = 'journal_logs';
    protected $fillable = [
        'url',
        'response_code',
        'response',
        'logs'
    ];
}
