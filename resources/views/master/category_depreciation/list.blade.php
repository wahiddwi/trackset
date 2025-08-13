@extends('adminlte::page')

@section('title', 'Master Category Depreciation')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Master Data
                    <small>
                        Category Depreciation
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Category Depreciation</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
            @can($menuId . '_create')
                <a href="{{ route('cat-depreciations.create') }}" class="btn btn-primary">Add</a> <br><br>
            @endcan

        <table id="category_depreciation_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Periode</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Update</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

@stop
@section('plugins.Datatables', true)
@section('css')
    <style>
        .btn-group {
            text-align: right;
            float: right;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script>
        let tb;
        function setDatatable(){
            tb = $('#category_depreciation_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('cat-depreciations.index') }}",
                columns: [
                    {data: 'dep_code', name: 'dep_code', width: '4%'},
                    {data: 'dep_periode', name: 'dep_periode', className: 'dt-center', width: '2%',
                        render: function (data) {
                            // console.log(data);
                            if (data == null) {
                                return '-';
                            } else {
                                return data;
                            }
                        }
                    },
                    {data: 'dep_type', name: 'dep_type', className: 'dt-center', width: '4%',
                        render: function (data) {                            
                            if (data == '') {
                                return 'Non Depreciation';
                            } else {
                                return data == 'month' ? 'Month' : 'Year';
                            }
                        }
                    },
                    {data: 'dep_active', name: 'dep_active', className: 'dt-center', width:'7%',
                    render: function(data, type){
                        if(type === 'display'){
                            if(data) {
                                return '<span class="badge badge-primary">Active</span>';
                            }
                            else{
                                return '<span class="badge badge-danger">Inactive</span>';
                            }
                        }
                        return data;
                    }},
                    {data: 'dep_amount_periode', name: 'dep_amount_periode', className: 'dt-center', width: '4%',
                        render: function (data) {
                            if (data) {
                                return data > 1 ? data + ' Months' : data + ' Month';
                            } else {
                                return 'Non Depreciation';
                            }
                        }
                    },
                    {data: 'updated_at', name: 'updated_at', width:'10%',
                    render: function(data){
                        return moment(data).format('DD MMM yyyy');
                    }},
                    {
                        data: 'id',
                        width: '1%',
                        orderable: false,
                        filterable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let disabled = '{{ auth()->user()->can($menuId . '_update') }}' ? '' : 'd-none';
                            return '<div class="btn-group">' +
                                '<a href="{{ url('cat-depreciations') }}/' + data +
                                '/edit" class="btn btn-secondary ' + disabled +
                                '" data-toggle="tooltip" title="View"><i class="fa fa-pen"></i></a>' +
                                '<button onclick="return toggleState(' + data + ');" class="btn btn-' + (row
                                    .dep_active ? "danger" : "primary") + disabled +
                                '" data-toggle="tooltip" title="' + (row.dep_active ? "Disable" :
                                    "Enable") + '"><i class="fa ' + (row.dep_active ? "fa-trash" :
                                    "fa-check") +
                                '"></i></button>'
                            '</div>';
                        }
                    },
                ],
                oLanguage: {sProcessing:
                    '<span class="info-box-icon">' +
                        '<img src="{{ asset("assets/img/RGLogo.png") }}" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" width="200" alt="">' +
                    '</span>' +
                    '<div class="info-box-content">' +
                        '<span class="info-box-text">Loading</span>' +
                    '</div>'
                }
            });
        }

        $(function(){
            setDatatable();
        });

        function toggleState(id) {
            let url = "{{ route('depreciations.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                    if (res.res) {
                        toastr.success('Kategori Penyusutan berhasil diupdate', 'Success');
                        tb.fnDraw();
                    } else {
                        toastr.error('mohon coba sesaat lagi.', 'Error');
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
                }
            });
        }
    </script>
@stop
