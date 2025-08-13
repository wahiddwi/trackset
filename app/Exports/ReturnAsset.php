<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ReturnAsset implements FromArray, WithHeadings, WithColumnFormatting, WithMapping
{
    public function __construct($data)
    {
      $this->data = $data;
    }

    public function array(): array
    {
      return $this->data;
    }
    
    public function headings(): array
    {
      return [
        'Kategori',
        'Cabang',
        'Lokasi',
        'Nama Barang',
        'Short Name',
        'Tipe PIC',
        'PIC',
        'Tgl Perolehan',
        'Harga',
        'Keterangan',
        'Serial Number',
        'Dok Referensi',
        'Brand',
        'Tag',
        'Kode Asset',
        'ID',
      ];
    }

    public function columnFormats(): array
    {
      return [
        'H' => NumberFormat::FORMAT_DATE_DATETIME,
      ];
    }

    public function map($row): array
    {
      return [
        $row['kategori'],
        $row['cabang'],
        $row['lokasi'],
        $row['nama_barang'],
        $row['short_name'],
        $row['tipe_pic'],
        $row['pic'],
        date('m/d/Y H:i:s A', strtotime($row['tgl_perolehan'])),
        $row['harga'],
        $row['keterangan'],
        $row['serial_number'],
        $row['dok_referensi'],
        $row['brand'],
        $row['tag'],
        $row['kode'],
        $row['id'],
      ];
    }
}
