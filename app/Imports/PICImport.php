<?php

namespace App\Imports;

use App\Models\Pic;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PICImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Pic([
            'pic_nik' => $row['pic_nik'],
            'pic_name' => $row['pic_name'],
            'pic_status' => TRUE,
        ]);
    }

    public function rules(): array
    {
      return [
        'pic_nik' => 'required|unique:pic,pic_nik',
        'pic_name' => 'required|string|max:50'
      ];
    }

    public function customValidationMessages()
    {
      return [
        'pic_nik.required' => 'NIK tidak boleh kosong!.',
        'pic_nik.unique' => 'NIK sudah digunakan oleh PIC lain.',
        'pic_name.required' => 'Nama tidak boleh kosong!',
        'pic_name.max' => 'Nama terlalu panjang',
      ];
    }
}
