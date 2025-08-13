<?php

namespace App\Exports;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PurchaseExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return PurchaseDetail::query()->with('purchase', 'category', 'depreciation', 'user', 'purchase.site');
    }

    public function map($details):array
    {
        // dd(date('d-m-Y', strtotime($details->purchase->purchase_date)));
        $months = $details->depreciation->dep_amount_periode >= 1 ? 'Months' : 'Month';
        return [
            [
                $details->purchase->purchase_id, // kode pembelian
                date('d-m-Y', strtotime($details->purchase->purchase_date)), // tgl. pembelian
                $details->purchase->purchase_pic == $details->user->usr_nik ? $details->user->usr_name : $details->purchase->site->si_name, // pic
                $details->purchase_detail_name, // item
                'Rp. ' .number_format($details->purchase_detail_price, 0,',','.'), // harga
                $details->category->cat_name, // category
                $details->depreciation->dep_periode.' '.$details->depreciation->dep_type.' '.$details->depreciation->dep_amount_periode.' '.$months, // depresiasi
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Pembelian',
            'Tgl. Pembelian',
            'PIC',
            'Item',
            'Harga',
            'Kategori',
            'Depresiasi'
        ];
    }
}
