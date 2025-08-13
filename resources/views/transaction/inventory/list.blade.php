@extends('adminlte::page')

@section('title', 'Data Asset')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Data
                    <small>
                        Asset
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Data Asset</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div id="alert">

        </div>
        <div class="row">
            <x-adminlte-input name="search" label="Search" placeholder="search...." id="search" igroup-size="md" fgroup-class="col-md-6" error-key="search">
                <x-slot name="appendSlot">
                    <a href="javascript:" type="search" class="btn btn-sm btn-outline-primary" id="btnSearch">Go!</a>
                </x-slot>
                <x-slot name="prependSlot">
                    <div class="input-group-text text-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
            {{-- <div class="col-md-7">
                <span class="text-md text-danger" id="validate" hidden="true">
                    No.Asset tidak tersedia.
                </span>
            </div> --}}
        </div>
    </div>
</div>

<div class="row" id="rowInventory">
    <div class="col-sm-6">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <strong>Tgl. Perolehan : </strong>
                        <span id="obtaindate"></span>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <strong>Cabang : </strong>
                        <span id="cabang"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Lokasi : </strong>
                        <span id="inv_location"></span>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <strong>Tipe User : </strong>
                        <span id="inv_pic_type"></span>
                    </div>
                    <div class="col-md-6 mt-1">
                        <strong>PIC : </strong>
                        <span id="pic"></span>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-12">
                        <strong>Description : </strong>
                        <span id="inv_desc"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="card">
            <div class="card-header">
                <strong>
                    <span id="inv_transno"></span>
                </strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nama Item : </strong>
                        <span id="inv_name"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Kategori : </strong>
                        <span id="category"></span>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <strong>Price : </strong>
                        <span id="price"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Depreciation : </strong>
                        <span id="depreciation"></span>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6">
                        <strong>Imei/SN : </strong>
                        <span id="inv_sn"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Doc. Referensi : </strong>
                        <span id="inv_doc_ref"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" id="rowHistory">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">History</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="historyTable">
                        <thead>
                            <th>#</th>
                            <th>Cabang</th>
                            <th>Lokasi</th>
                            {{-- <th>Item Name</th> --}}
                            <th>PIC</th>
                            <th>Status</th>
                            {{-- <th>doc. Referensi</th> --}}
                            <th>Tgl. Update</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="modal fade" id="showHistoryModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History Asset</h5>
                @can($menuId . '_print')
                    <div class="btn-group">
                        <a href="" id="printDetail" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Print</a>
                    </div>
                @endcan
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="detailClose();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped responsive">
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>Code Pembelian</strong></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;"><strong>Tgl. Pembelian</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;" id="purchase_id"></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;" id="purchase_date"></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>Cabang</strong></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;"><strong>Lokasi</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;" id="purchase_site"></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;" id="purchase_loc"></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>PIC</strong></td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 49%; text-align: center;" id="purchase_pic"></td>

                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>Description</strong></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;"><strong></strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%;" colspan="3" id="purchase_desc"></td>
                    </tr>
                </table>

                <h6>Details</h6>
                <table id="detail" class="table table-striped table-bordered responsive">
                    <thead>
                        <th>Detail Purchase Code</th>
                        <th>Name</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Harga</th>
                        <th>Barcode</th>
                    </thead>
                    <tbody id="detailList">
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

            <div class="overlay" id="load-overlay">
                <i class="fas fa-2x fa-sync-alt fa-spin"></i>
            </div>
        </div>
    </div>
</div> --}}

@stop
@section('plugins.Datatables', true)
@section('css')
    {{-- <link href='https://cdn.jsdelivr.net/npm/sweetalert2@10.10.1/dist/sweetalert2.min.css'> --}}
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
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script> --}}
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).ready( function () {
            $('#rowInventory').hide();
            $('#rowHistory').hide();

            function search(){
                var search = $('#search').val();
                $.ajax({
                    type: "GET",
                    url: "{{ url('inventory/search') }}",
                    data: {
                        search: search
                    },
                    dataType: "json",
                    success: function (res) {
                        $('#rowInventory').show();
                        $('#rowHistory').show();
                        // $('#validate').prop('hidden', true)


                        if(res.res == true){
                            var data = res.inv;
                            let price = new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                "currency": "IDR"
                            }).format(data.inv_price);


                            // console.log(data);
                            let plurals = data.category.depreciation.dep_amount_periode > 1 ? 'Months':'Month';

                            $('#inv_id').text(data.inv_id);
                            $('#inv_pic_type').text(data.inv_pic_type);
                            $('#inv_desc').text(data.inv_desc);
                            $('#inv_transno').text(data.inv_transno);
                            $('#inv_sn').text(data.inv_sn);
                            $('#inv_name').text(data.inv_name);
                            $('#inv_doc_ref').text(data.inv_doc_ref);
                            $('#obtaindate').text(moment(data.inv_obtaindate).format('DD/MM/YYYY'));
                            $('#cabang').html(data.site.si_name);
                            $('#inv_location').text(data.location.loc_name);
                            $('#pic').text(data.inv_pic == data.site.si_site ? data.site.si_name : data.user.usr_name );
                            $('#price').text(price);
                            $('#category').text(data.category.cat_name);
                            $('#depreciation').text(data.category.depreciation.dep_amount_periode+' '+plurals);

                            let elements = '';

                            for (let i = 0; i < data.history.length; i++) {
                                let no = (i+1);
                                let histSite = data.history[i].site.si_name;
                                let histLoc = data.history[i].location.loc_name;
                                // let histName = data.history[i].invhist_name;
                                let histPIC = data.history[i].invhist_pic == data.history[i].site.si_site ? data.history[i].site.si_name : data.history[i].user.usr_name;
                                let histStatus = data.history[i].invhist_status == 'ONHAND' ? '<span class="badge badge-primary">'+ data.history[i].invhist_status +'</span>' : '<span class="badge badge-warning">'+ data.history[i].invhist_status +'</span>';
                                // let histDocRef = data.history[i].invhist_doc_ref;
                                let updatedAt = data.history[i].updated_at;

                                elements += '<tr>';
                                elements += '<td>'+ no +'</td>';
                                elements += '<td>'+ histSite +'</td>';
                                elements += '<td>'+ histLoc +'</td>';
                                // elements += '<td>'+ histName +'</td>';
                                elements += '<td>'+ histPIC +'</td>';
                                elements += '<td>'+ histStatus +'</td>';
                                // elements += '<td>'+ histDocRef +'</td>';
                                elements += '<td>'+ moment(updatedAt).format('DD MMM yyyy'); +'</td>';
                            }
                            $('#historyTable tbody').html(elements);
                        }
                        else{
                            // ga ada data
                            $('#rowInventory').hide();
                            $('#rowHistory').hide();
                            if (res.res == false) {
                                $('#alert').html(`
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                            <span id="alertMsg">${res.message}</span>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                `);
                            }

                        }
                    }
                });
            }

            $('#search').on('keypress', function (e) {
               if(e.keyCode == 13) search();
            });

            $('#btnSearch').on('click', function () {
                search();
            })
        });

    </script>
@stop
