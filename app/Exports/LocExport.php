<?php

namespace App\Exports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LocExport implements FromArray, WithTitle, ShouldAutoSize
{
  public function title(): string
  {
    return 'Location Template';
  }

  public function array(): array
  {
    return [
      ['Cabang', 'Kode Lokasi', 'Nama Lokasi'],
      ['H01', 'H01-001', 'Gadai Jadi Berkah'],
      ['001', '001-001', 'Outlet Merdeka'],
      ['H02', 'H02-001', 'Amanah Terima Gadai']
    ];
  }
}
