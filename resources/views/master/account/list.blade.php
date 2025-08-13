@extends('adminlte::page')

@section('title', 'Master Account')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Master Data
                    <small>
                        Account
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Account</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @can($menuId . '_post')
            {{-- <a href="{{ route('account.create') }}" class="btn btn-primary">Add</a> <br><br> --}}
            <a href="{{ route('account.sync') }}" class="btn btn-primary">Sync</a> <br><br>
        @endcan

        <table id="account_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Update</th>
                    {{-- <th>&nbsp;</th> --}}
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
            tb = $('#account_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('account.index') }}",
                columns: [
                    {data: 'coa_account', name: 'coa_account', width: '4%'},
                    {data: 'coa_name', name: 'coa_name', className: 'dt-center', width: '2%'},
                    {data: 'coa_status', name: 'coa_status', className: 'dt-center', width:'7%',
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
                    {data: 'updated_at', name: 'updated_at', width:'10%',
                    render: function(data){
                        return moment(data).format('DD MMM yyyy');
                    }},
                    // {
                    //     data: 'id',
                    //     width: '1%',
                    //     orderable: false,
                    //     filterable: false,
                    //     searchable: false,
                    //     render: function(data, type, row) {
                    //         let disabled = '{{ auth()->user()->can($menuId . '_update') }}' ? '' : 'd-none';
                    //         return '<div class="btn-group">' +
                    //             '<a href="{{ url('account') }}/' + row.coa_account +
                    //             '/edit" class="btn btn-secondary ' + disabled +
                    //             '" data-toggle="tooltip" title="View"><i class="fa fa-pen"></i></a>' +
                    //             '<button onclick="return toggleState(' + row.coa_account + ');" class="btn btn-' + (row
                    //                 .coa_status ? "danger" : "primary") + disabled +
                    //             '" data-toggle="tooltip" title="' + (row.coa_status ? "Disable" :
                    //                 "Enable") + '"><i class="fa ' + (row.coa_status ? "fa-trash" :
                    //                 "fa-check") +
                    //             '"></i></button>'
                    //         '</div>';
                    //     }
                    // },
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
            let url = "{{ route('account.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                    if (res.res) {
                        toastr.success('Account berhasil diupdate', 'Success');
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
