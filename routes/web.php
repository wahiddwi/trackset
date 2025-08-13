<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PicController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Journal\LogController;
use App\Http\Controllers\ReportAssetController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\GeneralParamController;
use App\Http\Controllers\Journal\JournalController;
// use App\Http\Controllers\Journal\SellingController;
use App\Http\Controllers\CategoryMaintenanceController;
use App\Http\Controllers\Transaction\RequestController;
use App\Http\Controllers\Transaction\SellingController;
use App\Http\Controllers\Depreciation\PeriodeController;
use App\Http\Controllers\Transaction\DisposalController;
use App\Http\Controllers\Transaction\TransferController;
use App\Http\Controllers\Transaction\MaintenanceController;
use App\Http\Controllers\Transaction\AssetRequestController;
use App\Http\Controllers\Depreciation\DepreciationHistoryController;
use App\Http\Controllers\Journal\AssetController as JournalAssetController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout']);
Route::post('/change_password', [LoginController::class, 'changePassword']);

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/changesite/{site}', [GlobalController::class, 'changeSite'])->name('changesite');

Route::group(['middleware' => ['auth']], function () {
  // Modules routing
  Route::post('modules/{id}/toggle', [ModuleController::class, 'toggleState'])->middleware('ajax')->name('modules.toggle');
  Route::resource('modules', ModuleController::class)->except(['destroy']);

  // Users routing
  Route::group(['controller' => UserController::class, 'as' => 'users.', 'prefix' => 'users'], function () {
    Route::post('prv-update', 'privilegesUpdate')->middleware('ajax')->name('prv-update');
    Route::get('privileges/{id}', 'getPrivileges')->middleware('ajax')->name('privileges');
    Route::post('{id}/toggle', 'toggleState')->middleware('ajax')->name('toggle');
  });
  Route::resource('users', UserController::class)->except(['destroy', 'show']);

  // Roles routing
  Route::group(['controller' => RoleController::class, 'as' => 'roles.', 'prefix' => 'roles'], function () {
    Route::post('prv-update', 'privilegesUpdate')->middleware('ajax')->name('prv-update');
    Route::get('privileges/{id}', 'getPrivileges')->middleware('ajax')->name('privileges');
    Route::post('{id}/toggle', 'toggleState')->middleware('ajax')->name('toggle');
  });
  Route::resource('roles', RoleController::class)->except(['destroy', 'show']);

  // Site + Company routing
  Route::get('companies/sync', [CompanyController::class, 'syncCompany'])->name('companies.sync');
  Route::resource('companies', CompanyController::class)->only(['index']);
  Route::get('sites/sync', [SiteController::class, 'syncSite'])->name('sites.sync');
  Route::resource('sites', SiteController::class)->only(['index']);

  // Master Location
  Route::post('location/{id}/toggle', [LocationController::class, 'toggleState'])->middleware('ajax')->name('location.toggle');
  Route::post('location/import', [LocationController::class, 'import'])->name('loc.import');
  Route::get('location/download', [LocationController::class, 'downloadTemplate'])->name('loc.download');
  Route::resource('location', LocationController::class)->except(['destroy', 'show']);

  // Category
  Route::post('categories/{id}/toggle', [CategoryController::class, 'toggleState'])->middleware('ajax')->name('categories.toggle');
  Route::resource('categories', CategoryController::class)->except(['destroy', 'show']);

  // Category Depreciation
  Route::post('cat-depreciations/{id}/toggle', [CategoryDepreciationController::class, 'toggleState'])->middleware('ajax')->name('depreciations.toggle');
  Route::resource('cat-depreciations', CategoryDepreciationController::class)->except(['destroy', 'show']);

  // Inventory

  Route::get('inventory/search', [InventoryController::class, 'search']);
  Route::resource('inventory', InventoryController::class)->except(['destroy', 'create', 'edit', 'update']);

  // Maintenance
  Route::get('maintenance/search', [MaintenanceController::class, 'search'])->name('maintenance.search');
  Route::post('maintenance/{id}/post', [MaintenanceController::class, 'post'])->name('maintenance.post');
  Route::post('maintenance/{id}/toggle', [MaintenanceController::class, 'toggleState'])->middleware('ajax')->name('maintenance.toggle');
  Route::get('maintenance/{id}/history', [MaintenanceController::class, 'history'])->name('maintenance.history');
  Route::get('maintenance/{id}/print', [MaintenanceController::class, 'print'])->name('maintenance.print');
  Route::get('maintenance/getLastMaintenanceDate', [MaintenanceController::class, 'getLastMaintenanceDate'])->name('maintenance.lastdate');
  Route::resource('maintenance', MaintenanceController::class);

  // purchase
//   Route::get('purchase/{id}/accept', [PurchaseController::class, 'acceptPurchase']);
//   Route::patch('purchase/{id}/reject', [PurchaseController::class, 'rejectPurchase']);
//   Route::patch('purchase/{id}/disabled', [PurchaseController::class, 'disablePurchase']);
//   Route::get('purchase/{id}/barcode', [PurchaseController::class, 'barcodeItem']);
//   Route::get('purchase/{id}/print-detail', [PurchaseController::class, 'purchaseDetailPrint']);
//   Route::get('purchase/print', [PurchaseController::class, 'purchasePrint']);
//   Route::get('purchase/download-pdf', [PurchaseController::class, 'downloadPDF']);
//   Route::get('purchase/export-csv', [PurchaseController::class, 'exportCSV']);
//   Route::post('purchase/update/{id}', [PurchaseController::class, 'updateMultiple'])->name('purchase.update-multiple');
//   Route::resource('purchase', PurchaseController::class)->except(['destroy', 'update']);

  // Account
  Route::post('account/{id}/toggle', [AccountController::class, 'toggleState'])->middleware('ajax')->name('account.toggle');
  Route::get('account/sync_account', [AccountController::class, 'syncAccount'])->name('account.sync');
  Route::resource('account', AccountController::class)->except(['destroy', 'show']);

  // Asset
    Route::post('asset/getlocation', [AssetController::class, 'getLocation']);
    Route::get('asset/get-type', [AssetController::class, 'getType']);
    Route::post('asset/get-category', [AssetController::class, 'getCategory']);
    Route::get('asset/{id}/accept', [AssetController::class, 'accept'])->name('asset.post');
    Route::post('asset/{id}/remove', [AssetController::class, 'remove']);
    Route::get('asset/{id}/qr', [AssetController::class, 'qr'])->name('asset.qr');
    Route::get('asset/{id}/barcode', [AssetController::class, 'barcode'])->name('asset.barcode');
    Route::post('asset/import', [AssetController::class, 'import'])->name('asset.import');
    Route::get('asset/download', [AssetController::class, 'downloadTemplate'])->name('asset.download');
    Route::get('asset/download-tag', [AssetController::class, 'export_tag'])->name('asset.download-tag');
    Route::get('asset/download-merk', [AssetController::class, 'export_merk'])->name('asset.download-merk');
    Route::post('asset/upload', [AssetController::class, 'file_upload'])->middleware('ajax')->name('asset.file-upload');
    Route::post('asset/delete', [AssetController::class, 'file_delete'])->middleware('ajax')->name('asset.file-delete');
    Route::resource('asset', AssetController::class);

    // Transfer
    Route::get('transfer/{id}/print', [TransferController::class, 'print'])->name('transfer.print');
    Route::get('transfer/{id}/accept', [TransferController::class, 'accept'])->name('transfer.post');
    Route::delete('transfer/{id}/remove', [TransferController::class, 'remove']);
    Route::get('transfer/getdetail/{id}', [TransferController::class, 'getDetail'])->name('transfer.detail');
    Route::post('transfer/update/{id}', [TransferController::class, 'updateMultiple'])->name('transfer.update-multiple');
    Route::get('transfer/search', [TransferController::class, 'search'])->name('transfer.search');
    Route::get('transfer/get-type', [TransferController::class, 'getType']);
    // Route::post('transfer/getlocation', [TransferController::class, 'getLocation']);
    // Route::get('/get-assets-by-branch', [TransferController::class, 'getAssetsByBranch']);
    Route::get('transfer/get-assets-by-site', [TransferController::class, 'getAssetBySite'])->name('transfer.get-assets-by-site');
    Route::get('transfer/reset-select', [TransferController::class, 'resetSelectSite'])->name('transfer.reset-select-site');

    Route::get('transfer/get-location', [TransferController::class, 'getLocation'])->name('transfer.getlocation');
    Route::post('transfer/{id}/toggle', [TransferController::class, 'toggleState'])->middleware('ajax')->name('transfer.toggle');
    Route::post('transfer/{id}/accept', [TransferController::class, 'accept'])->middleware('ajax')->name('transfer.accept');
    Route::resource('transfer', TransferController::class)->except(['destroy']);

    // Receive
    Route::get('receive/{id}/print-all', [ReceiveController::class, 'printAll'])->name('receive.print-all');
    Route::get('receive/{id}/print', [ReceiveController::class, 'print']);
    Route::get('receive/{id}', [ReceiveController::class, 'detail']);
    Route::get('receive/{id}/accept', [ReceiveController::class, 'accept']);
    Route::get('receive', [ReceiveController::class, 'index'])->name('receive.index');

    // vehicle
    // Route::delete('vehicle/{id}/delete', [VehicleController::class, 'delete']);
    Route::post('vehicle/upload', [VehicleController::class, 'file_upload'])->middleware('ajax')->name('vehicle.file-upload');
    Route::post('vehicle/delete', [VehicleController::class, 'file_delete'])->middleware('ajax')->name('vehicle.file-delete');
    Route::get('vehicle/search', [VehicleController::class, 'search'])->name('vehicle.search');
    Route::resource('vehicle', VehicleController::class)->except(['destroy']);

    // Depreciation History
    Route::post('depreciation-history/create', [DepreciationHistoryController::class, 'store']);

    // Periode
    Route::get('periode', [PeriodeController::class, 'index'])->name('periode');

    // Journal/Asset
    Route::get('journal-asset/getsite', [JournalAssetController::class, 'getSite'])->name('journal.get-site');
    Route::get('journal-asset', [JournalAssetController::class, 'index']);
    Route::get('journal-asset/search', [JournalAssetController::class, 'search']);

    // JOurnal Logs
    Route::get('logs', [LogController::class, 'index'])->name('logs');

    // Disposal
    // Route::get('disposal', [DisposalController::class, 'index'])->name('disposal.index');
    // Route::post('disposal/search', [DisposalController::class, 'search'])->name('disposal.search');
    // Route::post('disposal/store', [DisposalController::class, 'store'])->name('disposal.store');
    Route::post('disposal/{id}/accept', [DisposalController::class, 'accept'])->name('disposal.accept');
    Route::get('disposal/search', [DisposalController::class, 'search'])->name('disposal.search');
    Route::post('disposal/{id}/toggle', [DisposalController::class, 'toggleState'])->middleware('ajax')->name('disposal.toggle');
    Route::resource('disposal', DisposalController::class)->except(['destroy']);

    // Master Customer
    Route::post('customer/{id}/toggle', [CustomerController::class, 'toggleState'])->middleware('ajax')->name('customer.toggle');
    Route::resource('customer', CustomerController::class)->except(['destroy']);

    // selling
    Route::post('selling/{id}/toggle', [SellingController::class, 'toggleState'])->middleware('ajax')->name('selling.toggle');
    Route::post('selling/{id}/accept', [SellingController::class, 'accept'])->name('selling.accept');
    Route::get('selling/search', [SellingController::class, 'search'])->name('selling.search');
    Route::resource('selling', SellingController::class);

    // calculate Depreciation
    // Route::get('cal-dep', [CalculateDepreciationController::class, 'index'])->name('calculate.index');
    // Route::get('cal-dep/create', [CalculateDepreciationController::class, 'create'])->name('calculate.create');
    // Route::get('cal-dep', [CalculateDepreciationController::class, 'index'])->name('calculate.index');
    // Route::get('cal-dep/get-company', [CalculateDepreciationController::class, 'getCompany'])->name('calculate.get-company');
    // Route::post('cal-dep/calculate', [CalculateDepreciationController::class, 'calculate'])->name('calculate.cal');

    // Master Brand
    Route::post('brand/{id}/toggle', [BrandController::class, 'toggleState'])->middleware('ajax')->name('brand.toggle');
    Route::resource('brand', BrandController::class)->except(['destroy']);
    
    // master tags
    Route::post('tag/{id}/toggle', [TagController::class, 'toggleState'])->middleware('ajax')->name('tag.toggle');
    Route::resource('tag', TagController::class)->except(['destroy', 'show']);

    // master vendor
    Route::post('agent/{id}/toggle', [VendorController::class, 'toggleState'])->middleware('ajax')->name('agent.toggle');
    Route::resource('agent', VendorController::class)->except(['destroy']);

    // master asuransi
    Route::post('insurance/upload', [InsuranceController::class, 'file_upload'])->middleware('ajax')->name('insurance.file-upload');
    Route::post('insurance/delete', [InsuranceController::class, 'file_delete'])->middleware('ajax')->name('insurance.file-delete');

    // Journal
    Route::post('journal/asset/create', [JournalController::class, 'journalAsset']);

    // Master PIC
    Route::post('pic/{id}/toggle', [PicController::class, 'toggleState'])->middleware('ajax')->name('pic.toggle');
    Route::post('pic/import', [PicController::class, 'import'])->name('pic.import');
    Route::get('pic/download', [PicController::class, 'downloadTemplate'])->name('pic.download');
    Route::resource('pic', PicController::class)->except(['destroy', 'show']);

    // General Params
    Route::get('params', [GeneralParamController::class, 'index'])->name('params.index');
    Route::patch('params/{id}/update', [GeneralParamController::class, 'update'])->name('params.update');

    // Depreciation
    Route::get('depre', [DepreciationController::class, 'index'])->name('depre.index');
    Route::get('depre/create', [DepreciationController::class, 'create'])->name('depre.create');
    Route::get('depre/get-company', [DepreciationController::class, 'getCompany'])->name('depre.get-company');
    Route::post('depre/calculate', [DepreciationController::class, 'calculate'])->name('depre.calculate');
    Route::get('depre/{id}/create-journal', [DepreciationController::class, 'createJournal'])->name('depre.journal');

    // Report
    Route::get('report', [ReportAssetController::class, 'index'])->name('report.index');
    Route::get('report/filter', [ReportAssetController::class, 'filter'])->name('report.filter');
    Route::get('report/{id}/show', [ReportAssetController::class, 'show'])->name('report.show');
    Route::get('report/export', [ReportAssetController::class, 'export'])->name('report.export');
    Route::get('report/get-pic', [ReportAssetController::class, 'getPic'])->name('report.get-pic');
    Route::get('report/get-loc', [ReportAssetController::class, 'getLocation'])->name('report.get-loc');
    Route::get('report/{id}/edit', [ReportAssetController::class, 'edit'])->name('report.edit');
    Route::patch('report/{id}', [ReportAssetController::class, 'update'])->name('report.update');
    Route::post('report/{id}/delete', [ReportAssetController::class, 'delete'])->name('report.delete');

    // Stock Opname
    // Route::post('stock_opname/create-header', [StockOpnameController::class, 'createHeader'])->name('stock.createheader');
    Route::post('stock_opname/getlocation', [StockOpnameController::class, 'getLocation'])->name('stock.getlocation');
    Route::post('stock_opname/scan', [StockOpnameController::class, 'scan'])->name('stock.scan');
    Route::get('stock_opname/checkExistingAsset', [StockOpnameController::class, 'checkExistingAsset'])->name('stock.checkExistingAsset');
    Route::post('stock_opname/approve', [StockOpnameController::class, 'approve'])->name('stock.approve');
    Route::get('stock_opname/check-loc', [StockOpnameController::class, 'checkingLoc'])->name('stock.check-loc');
    Route::get('stock_opname/{id}/approval-note', [StockOpnameController::class, 'getApprovalNote'])->name('stock.approval-note');
    Route::post('stock_opname/{id}/accept', [StockOpnameController::class, 'accept'])->name('stock.accept');
    Route::post('stock_opname/{id}/reject', [StockOpnameController::class, 'reject'])->name('stock.reject');
    Route::get('stock_opname/get-additional-items/{id}', [StockOpnameController::class, 'getAdditionalItems'])->middleware('ajax')->name('stock.get-additional-items');
    Route::resource('stock_opname', StockOpnameController::class);

    // Asset Request
    Route::get('asset_request/{id}/close', [RequestController::class, 'close_spb'])->name('asset_request.close_spb');
    Route::post('asset_request/transfer', [RequestController::class, 'transferProcess'])->name('asset_request.transfer');
    Route::get('asset_request/{id}/purchase', [RequestController::class, 'purchase_asset'])->name('asset_request.purchase');
    Route::post('asset_request/purchase', [RequestController::class, 'purchaseProcess'])->name('asset_request.purchase_process');
    Route::post('asset_request/buy', [RequestController::class, 'buyAsset'])->name('asset_request.buy');
    Route::resource('asset_request', RequestController::class);

    // Category Maintenance
    Route::post('cat_maintenance/{id}/toggle', [CategoryMaintenanceController::class, 'toggleState'])->middleware('ajax')->name('cat_maintenance.toggle');
    Route::resource('cat_maintenance', CategoryMaintenanceController::class)->except(['show', 'destroy']);
});
