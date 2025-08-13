<?php

namespace App\Exports;

use DateTime;
use Carbon\Carbon;
use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AssetExport implements FromArray, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function title(): string
    {
      return 'ASSET TEMPLATE';
    }

    public function array(): array
    {
      return [
        ['Kategori', 'Cabang', 'Lokasi', 'Nama Barang', 'Short Name', 'Tipe PIC', 'PIC', 'Tgl Perolehan', 'Harga', 'Keterangan', 'Serial Number', 'Dok Referensi', 'Brand', 'Tag'],
        ['INV01', 'H01', 'RK9-LT1', 'SAMSUNG A30S', 'SMSNG A30S', 'cabang', 'H01', Date::dateTimeToExcel(Carbon::now()), '3500000', 'TEST 1', 'asdqwe123', '-', 5, 1],
        ['INV01', 'H01', 'RK9-LT2', 'OPPO A54', 'OPPO A54', 'user', '2303073', Date::dateTimeToExcel(Carbon::now()), '3500000', 'TEST 1', 'asdqwe123', '-', 5, 1],
        ['KND01', 'P01', 'RK8-LT1', 'MITSUBISHI ELF', 'MITSU ELF', 'user', '2403035', Date::dateTimeToExcel(Carbon::now()), '90000000', '', '-', '-', 9, 3]
      ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    } 
}
