<?php

namespace App\Exports;

use App\Models\Pic;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PICExport implements FromArray, WithTitle, ShouldAutoSize
{
  public function title(): string
  {
    return 'PIC TEMPLATE';
  }

  public function array(): array
  {

    return [
      ['PIC_NIK', 'PIC_Name'],
      ['2403035', 'USER TEST 1'],
      ['2403036', 'USER TEST 2']
    ];
  }
}
