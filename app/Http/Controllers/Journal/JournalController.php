<?php

namespace App\Http\Controllers\Journal;

use Exception;
use App\Models\JournalLogs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JournalController extends Controller
{
  
    public function journalAsset($payload)
    {
      $apiUrl = config('app.api_url') . '/journal/build-journal';
      $token = env('API_KEY_INTERNAL');
    
      // // hit API
      $res = Http::withToken($token)
                  ->post($apiUrl, $payload);

      $statusCode = $res->status();

      $resBody = json_decode($res->getBody(), true);

      if ($statusCode == 500) {
        // throw new Exception("ERROR API.", 1);
        throw new HttpException(500, "ERROR API");
        
      }

      // create journal_log table
      // JournalLogs::create([
      //   'url' => $apiUrl,
      //   'response_code' => $statusCode,
      //   'response' => 'Journal Logs created successesfully!',
      //   'logs' => json_encode($payload),
      // ]);

        if ($res->json('result') === false) {
          throw new HttpException(500, $res->json('res_msg'));
        }

        return $res;
    }
}
