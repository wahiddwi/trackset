<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SyncController;
use App\Http\Controllers\Journal\AssetController;
use App\Http\Controllers\Api\V1\JournalController;
use App\Http\Controllers\Api\V1\AuthenticationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function() {
    Route::post('sync_site', [SyncController::class, 'syncSite']);
    Route::post('sync_company', [SyncController::class, 'syncCompany']);
    Route::post('payload_journal', [JournalController::class, 'payloadJournal']);
    Route::post('sync_account', [SyncController::class, 'syncAccount']);
});
