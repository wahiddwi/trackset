<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Pic;
use App\Models\Tag;
use App\Models\Site;
use App\Models\User;
use App\Models\Asset;
use App\Models\Brand;
use App\Models\Company;
use App\Models\InvHist;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Str;
use App\Models\GeneralParam;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetImport implements ToCollection, WithValidation, WithHeadingRow
{
  use Importable;
  public $data;

  public function __construct()
  {
    $this->data = [];
  }

  public function collection(Collection $rows)
  {
    foreach ($rows as $row) {
      $this->data[] = array(
        'kategori' => $row['kategori'],
        'cabang' => $row['cabang'],
        'lokasi' => $row['lokasi'],
        'nama_barang' => $row['nama_barang'],
        'short_name' => $row['short_name'],
        'tipe_pic' => $row['tipe_pic'],
        'pic' => $row['pic'],
        'tgl_perolehan' => Carbon::parse(Date::excelToDateTimeObject($row['tgl_perolehan'])),
        'harga' => $row['harga'],
        'keterangan' => $row['keterangan'],
        'serial_number' => $row['serial_number'],
        'dok_referensi' => $row['dok_referensi'],
        'brand' => $row['brand'],
        'tag' => $row['tag']
      );
    }
    return $this->data;
  }


  public function rules(): array
  {
    return [
      'tgl_perolehan' => 'required',
      'kategori' => 'required|exists:categories,cat_code',
      'cabang' => 'required|exists:sites,si_site',
      // 'lokasi' => 'required|exists:loc_mstr,loc_id',
      'lokasi' => 'required|exists:loc_mstr,loc_id',
      'nama_barang' => 'required|string|max:255',
      'short_name' => 'required|string|max:30',
      'tipe_pic' => 'required',
      'pic' => 'required',
      'harga' => 'required',
      'keterangan' => 'nullable|max:255',
      'serial_number' => 'required',
      'dok_referensi' => 'required',
      'brand' => 'required|exists:brand_mstr,id',
      'tag' => 'required|exists:tag_mstr,id',
      'pic_nik' => 'nullable|exists:pic,pic_nik',
      'si_site' => 'nullable|exists:sites,si_site',
      'loc_site' => [
        function($attr, $value, $fail) {
          $parts = explode('||', $value);

          $checkExisitingData = DB::table('loc_mstr')
                                  ->where('loc_site', '=', $parts[0])
                                  ->where('loc_id', '=', $parts[1])
                                  ->exists();

          if (!$checkExisitingData) {
            $fail('Lokasi tidak tersedia di cabang tersebut.');
          }
        }
      ],
    ];
  }

  public function customValidationMessages()
  {
    return [
      'kategori.required' => 'Kategori Tidak boleh kosong',
      'kategori.exists' => 'Kategori tersebut tidak terdaftar di master data.',
      'cabang.required' => 'Cabang tidak boleh kosong',
      'cabang.exists' => 'Cabang tidak terdaftar di master data',
      'lokasi.required' => 'Lokasi tidak boleh kosong',
      'lokasi.exists' => 'Lokasi tidak terdaftar di master data',
      'nama_barang.required' => 'Nama Barang tidak boleh kosong',
      'nama_barang.max' => 'Nama Barang terlalu panjang',
      'short_name.required' => 'Short Name tidak boleh kosong',
      'short_name.max' => 'Short Name terlalu panjang',
      'tipe_pic.required' => 'Tipe PIC tidak boleh kosong',
      'pic.required' => 'PIC tidak boleh kosong',
      'tgl_perolehan.required' => 'Tgl. Perolehan tidak boleh kosong',
      'tgl_perolehan.date' => 'Harus diisi dengan tanggal valid.',
      'harga.required' => 'Harga tidak boleh kosong',
      'keterangan.max' => 'Keterangan terlalu panjang',
      'serial_number.required' => 'Serial Number tidak boleh kosong',
      'serial_number.max' => 'Serial Number terlalu panjang',
      'dok_referensi.required' => 'Dokumen Referensi tidak boleh kosong',
      'dok_referensi.max' => 'Dokumen Referensi terlalu panjang',
      'brand.required' => 'Brand tidak boleh kosong',
      'brand.exists' => 'Brand tidak terdaftar di master data',
      'tag.required' => 'Tag tidak boleh kosong',
      'tag.exists' => 'Tag tidak terdaftar di master data',
      'pic_nik.exists' => 'PIC tidak terdaftar di master data',
      'si_site.exists' => 'Site tidak terdaftar di master data',
    ];
  }

  public function prepareForValidation($data, $index)
  {
    if ($data['tipe_pic'] == 'user') {
      $data['pic_nik'] = $data['pic'];
    } else {
      $data['si_site'] = $data['pic'];
    }

    $data['loc_site'] = $data['cabang'] .'||'. $data['lokasi'];

    return $data;
  }
}
