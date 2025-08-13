<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Asset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;

class ReportAssetExport implements FromView
{
  public function __construct($category, $brand, $tag, $pic, $loc, $date) {
    $this->category = $category;
    $this->brand = $brand;
    $this->tag = $tag;
    $this->pic = $pic;
    $this->loc = $loc;
    $this->date = $date;
  }

  public function view(): View
  {
    $query = Asset::select('id', 'inv_transno', 'inv_category', 'inv_site', 'inv_company', 'inv_loc', 'inv_depreciation',
                    'inv_name', 'inv_pic_type', 'inv_pic', 'inv_obtaindate', 'inv_price', 'inv_status', 'inv_desc', 'inv_sn',
                    'inv_doc_ref', 'inv_accumulate_dep', 'inv_nominal_dep', 'inv_end_date', 'inv_current_price', 'inv_dep_periode',
                    'inv_dep_amount', 'inv_tag', 'inv_merk', 'is_vehicle')
                    ->with('site', 'category', 'merk', 'tag', 'location', 'pic')
                    ->whereNotIN('inv_status', ['SELL', 'DISPOSAL']);

            if (!empty($this->category)) {
              $query->whereIn('inv_category', $this->category);
            }

            if (!empty($this->brand)) {
              $query->whereIn('inv_merk', $this->brand);
            }

            if (!empty($this->tag)) {
              $query->whereIn('inv_tag', $this->tag);
            }

            if (!empty($this->pic)) {
              $query->whereIn('inv_pic', $this->pic);
            }

            if (!empty($this->loc)) {
              $query->whereIn('inv_loc', $this->loc);
            }

            if (!empty($this->date)) {
              list($start, $end) = explode(' - ',  $this->date);

              $startDate = Carbon::parse($start);
              $endDate = Carbon::parse($end);

              $query->whereBetween('inv_obtaindate', [$startDate, $endDate]);
            }

      $reports = $query->get();

    return view('report.asset.export', [
      'reports' => $reports,
    ]);
  }
}
