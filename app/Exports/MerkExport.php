<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MerkExport implements FromQuery, WithTitle, WithMapping, WithHeadings
{
  public function title(): string
  {
    return 'List Master Merk';
  }

  public function query()
  {
    return Brand::query()
                ->active()
                ->orderby('id', 'asc')
                ->select('id', 'brand_name');
  }

  public function map($merk): array
  {
    return [
      $merk->id,
      $merk->brand_name,
    ];
  }

  public function headings(): array
  {
    return [
      'ID',
      'Brand Name',
    ];
  }
}
