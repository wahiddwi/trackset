@extends('adminlte::page')

@section('title', 'Transaksi Invetory')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Data History
                    <small>
                        Asset
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Data History Asset</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
                <strong>
                    {{ $inventory->inv_id }}
                </strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <strong>Tgl. Perolehan : </strong>
                        <span id="inv_obtaindate"></span>
                        {{ date('d-m-Y', strtotime($inventory->inv_obtaindate)) }}
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <strong>Cabang : </strong>
                        {{ $inventory->site->si_name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Lokasi : </strong>
                        {{ $inventory->location->loc_name }}
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <strong>Tipe User : </strong>
                        {{ $inventory->inv_pic_type }}
                    </div>
                    <div class="col-md-6 mt-1">
                        <strong>PIC : </strong>
                        {{ $inventory->inv_pic }}
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-12">
                        <strong>Description : </strong>
                        {{ $inventory->inv_desc }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
                <strong>
                    {{ $inventory->inv_transno }}
                </strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nama Item : </strong>
                        {{ $inventory->inv_name }}
                    </div>
                    <div class="col-md-6">
                        <strong>Kategori : </strong>
                        {{ $inventory->history[0]->category->cat_name }}
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <strong>Price : </strong>
                        {{ "Rp. " . number_format($inventory->inv_price,0,',','.'); }}
                    </div>
                    <div class="col-md-6">
                        <strong>Depreciation : </strong>
                        {{ $inventory->category->depreciation->dep_amount_periode }} Months
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <strong>Imei/SN : </strong>
                        {{ $inventory->inv_sn }}
                    </div>
                    <div class="col-md-6">
                        <strong>Doc. Referensi : </strong>
                        {{ $inventory->inv_doc_ref }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">History</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <th>#</th>
                            <th>Transno</th>
                            <th>Cabang</th>
                            <th>Lokasi</th>
                            <th>Item Name</th>
                            <th>PIC</th>
                            <th>Obtaindate</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Imei/SN</th>
                            <th>doc. Referensi</th>
                            <th>&nbsp;</th>
                        </thead>
                        <tbody>
                            @foreach ($inventory->history as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->invhist_transno }}</td>
                                    <td>{{ $item->site->si_name }}</td>
                                    <td>{{ $item->location->loc_name }}</td>
                                    <td>{{ $item->invhist_name }}</td>
                                    {{-- @dd($item->user->usr_nik) --}}
                                    <td>{{ $item->invhist_pic == $item->site->si_site ? $item->site->si_name : $item->user->usr_name }}</td>
                                    <td>{{ date('d-m-Y', strtotime($item->invhist_obtaindate)) }}</td>
                                    <td>{{ "Rp. " . number_format($item->invhist_price,0,',','.'); }}</td>
                                    <td>{{ $item->invhist_status }}</td>
                                    <td>{{ $item->invhist_desc }}</td>
                                    <td>{{ $item->invhist_sn }}</td>
                                    <td>{{ $item->invhist_doc_ref }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
@section('plugins.Datatables', true)
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
    <style>
        .btn-group {
            text-align: right;
            float: right;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).ready( function () {

        });

    </script>
@stop
