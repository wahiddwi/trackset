<?php

namespace App\Imports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LocImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Location([
            'loc_site' => $row['cabang'],
            'loc_id' => $row['kode_lokasi'],
            'loc_name' => $row['nama_lokasi'],
            'loc_active' => TRUE,
        ]);
    }

    public function rules(): array
    {
      return [
        'cabang' => 'required|exists:sites,si_site',
        'nama_lokasi' => 'required|string|max:60',
        'kode_lokasi' => 'required|unique:loc_mstr,loc_id',
        'compare' => [
          'required',
          function ($attr, $value, $fail) {
            $parts = explode('||', $value);
            $loc = substr($parts[1], 0, 3);

            if ($parts[0] != $loc) {
              $fail('Kode Lokasi tidak tersedia dicabang tersebut.');
            }
          }
        ],
      ];
    }

    public function customValidationMessages()
    {
      return [
        'cabang.required' => 'Cabang tidak boleh kosong',
        'cabang.exists' => 'Cabang tersebut tidak terdaftar di master data',
        'nama_lokasi.required' => 'Nama Lokasi tidak boleh kosong',
        'nama_lokasi.max' => 'Nama Lokasi terlalu panjang',
        'kode_lokasi.required' => 'Kode lokasi tidak boleh kosong',
        'kode_lokasi.unique' => 'Kode lokasi sudah digunakan',
      ];
    }

    public function prepareForValidation($data, $index)
    {
      $data['compare'] = $data['cabang'] .'||'. $data['kode_lokasi'];
      return $data;
    }
}
