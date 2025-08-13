<?php

namespace App\Http\Controllers\Api\V1;

use Throwable;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\Asset;
use GuzzleHttp\Client;
use App\Models\JournalLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DepreciationHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Controllers\Depreciation\PeriodeController;
use App\Http\Controllers\Depreciation\DepreciationHistoryController;

class JournalController extends Controller
{
    public function payloadJournal(Request $request)
    { 
      // hit API
      $this->postJournal($request->all());

      // create journal_log table
      // JournalLogs::create([
      //   'url' => 'http://localhost:8000/api/v1/journal-asset/search',
      //   'response_code' => 201,
      //   'response' => 'Journal Logs created successesfully!',
      //   'logs' => json_encode($journal),
      // ]);

      return;
    }

    public function postJournal($data)
    {
      $res = Http::withToken(
        config('app.internal_api_key')
        )->post(
          config('app.api_url') . '/journal/build-journal',
            $data,
        );
        
      return $res->body();
    }
}
