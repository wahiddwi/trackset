<?php

namespace App\Http\Controllers\Journal;

use Throwable;
use Carbon\Carbon;
use App\Models\Site;
use App\Models\Asset;
use App\Models\Module;
use App\Models\Company;
use App\Models\JournalLogs;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Models\DepreciationHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Haruncpi\LaravelIdGenerator\IdGenerator;
// use App\Http\Controllers\Api\V1\JournalController;
use App\Http\Controllers\Journal\JournalController;
use App\Http\Controllers\Depreciation\DepreciationHistoryController;

class AssetController extends Controller
{
    public function __construct() {
        $this->middleware(['permission']);
        $this->middleware(['permission:create'])->only(['create', 'store']);
        $this->middleware(['permission:update'])->only(['edit', 'update']);
        $this->middleware(['permission:print'])->only(['print']);
        $this->middleware(['permission:post'])->only(['post']);
    }

    public function index(Request $request)
    {
        $avail_site = array_keys(Session::get('available_sites')->toArray());
        $modules = Module::isSuper()->active()->where('mod_parent', null)->orderBy('mod_order', 'asc')->get();
        $menuId = $request->attributes->get('menuId');
        $company = Site::select('si_site', 'si_name', 'si_company')->with('company')->where('si_site', $avail_site)->active()->get();

        return view('journal.asset.index', compact('company', 'modules', 'menuId'));
    }

    public function search(Request $request)
    {
        $request->validate([
            'periode' => 'required',
            'company' => 'required',
            'site' => 'required',
        ]);

        // startdate
        $getStartDate = Asset::select('inv_obtaindate')->oldest('inv_obtaindate')->first();

        // get last day of month
        $lastDayOfMonth = Carbon::parse($request->periode)->endOfMonth()->toDateString();
        $getPrevMonth = Carbon::parse($request->periode)->endOfMonth()->subMonth(1);
        $sites = Site::select('si_site', 'si_name', 'si_company')
                    ->where('si_company', $request->company)->active()->get();

        $company = Company::select('co_company', 'co_name', 'last_dep')->whereIn('co_company', [$request->company])->first();

        foreach ($sites as $key => $site) {
            $companyList = [
                'si_site' => $site->si_site,
                'si_name' => $site->si_name,
                'si_company' => $site->si_company,
                'co_company' => $site->company->co_company,
                'co_name' => $site->company->co_name,
            ];
        }
        // kondisi periode dan PT yang ingin dibuat jurnal sudah ada
        if (DepreciationHistory::select('dephist_transdate', 'dephist_company')
            ->where('dephist_transdate', $lastDayOfMonth)
            ->where('dephist_company', $request->company)->exists()) {
            # success created journal, send payload data journal to API, created periode table, and created dpereciation table
            return response()->json([
                'res' => false,
                'message' => 'Jurnal pada periode atau PT tersebut sudah pernah dibuat.'
            ]);

        // kondisi periode bulan sebelumnya harus sudah dijurnal
        } elseif (DepreciationHistory::where('dephist_transdate', $getPrevMonth)->exists()) {
            # if previous periode not found
            return response()->json([
                'res' => false,
                'message' => 'Jurnal sebelum periode '. $request->periode .' belum dibuat, silahkan buat jurnal sebelum periode yg dipilih.'
            ]);

        // dan periode yang akan dibuat belum dijurnal
        } else {
            # success created journal, send payload data journal to API, created periode table, and created dpereciation table
            $search = DB::table('inv_mstr as inv')
                        ->join('categories as cat', 'inv.inv_category', '=', 'cat.id')
                        ->join('sites as si', 'inv.inv_site', '=', 'si.si_site')
                        ->join('companies as co', 'si.si_company', '=', 'co.co_company')
                        ->selectRaw('inv.inv_site, inv.inv_transno, inv.inv_price, inv.inv_accumulate_dep, inv.inv_nominal_dep,
                                    cat.cat_asset, cat.cat_income, cat.cat_disposal, co.co_company, si.si_site, inv.inv_end_date,
                                    co.last_dep
                                    inv.inv_current_price, cat.cat_accumulate_depreciation, cat.cat_depreciation_expense')
                        ->whereIn('inv_site', [$request->site])
                        ->whereIn('inv_company', [$request->company])
                        // ->whereBetween('inv_end_date', [$getStartDate[0]['inv_obtaindate'], $lastDayOfMonth])
                        // ->whereBetween('co.last_dep', [$company->last_dep, $lastDayOfMonth])
                        ->whereBetween('co.last_dep', [$getStartDate['inv_obtaindate'], $lastDayOfMonth])
                        ->groupBy('cat.cat_accumulate_depreciation', 'cat.cat_depreciation_expense', 'inv.inv_site',
                                  'inv.inv_transno', 'inv.inv_price', 'inv.inv_accumulate_dep', 'inv.inv_nominal_dep', 'inv.inv_end_date',
                                  'inv.inv_current_price', 'cat.cat_asset', 'cat.cat_income', 'cat.cat_disposal', 'co.co_company', 'si.si_site',
                                  'co.last_dep')
                        // ->groupBy('categories.cat_accumulate_depreciation', 'categories.cat_depreciation_expense')
                        ->get();

            $data = [];
            // $journalTop;
            $site;
            foreach ($search as $key => $value) {
                # data isi journal
                $site = $value->inv_site;
                array_push(
                    $data,
                    array(
                        'line' => $key+1,
                        'asset_transno' => $value->inv_transno,
                        'site' => $value->inv_site,
                        'price' => $value->inv_price,
                        'accumulate_dep' => $value->inv_accumulate_dep,
                        'nominal_dep' => $value->inv_nominal_dep,
                        'current_price' => $value->inv_current_price,
                        'account_asset' => $value->cat_asset,
                        'account_accumulate_depreciation' => $value->cat_accumulate_depreciation,
                        'account_dep_expense' => $value->cat_depreciation_expense,
                        'account_income' => $value->cat_income,
                        'account_disposal' => $value->cat_disposal,
                    ),
                );
            }

            // send payload to API
            $journal = [
              'hcompany' => $request->company,
              'company' => $request->company,
              'site' => $request->site,
              'transdate' => $request->periode,
              'detail' => $data,
            ];

            $payloadJournal = new JournalController();
            $payloadJournal->journalAsset($journal);

            return response()->json([
                'res' => true,
                'status' => 201,
                'message' => 'Jurnal pada periode '.$request->periode.' berhasil dibuat.'
            ]);
        }
    }

    public function getSite(Request $request)
    {
      $sites = Site::select('si_site', 'si_name', 'si_company')->where('si_company', $request->company)->get();
      return response()->json($sites);
    }
}
