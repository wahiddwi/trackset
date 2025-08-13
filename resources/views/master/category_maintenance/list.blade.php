@extends('adminlte::page')

@section('title', 'Master Category Maintenance')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Master
                    <small>
                        Category Maintenance
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Category Maintenance</li>
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
                <a href="{{ route('cat_maintenance.create') }}" class="btn btn-primary">Add</a> <br><br>
            @endcan

        <table id="cat_maintenance_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Status</th>
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
            tb = $('#cat_maintenance_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('cat_maintenance.index') }}",
                columns: [
                    {data: 'mtn_type', name: 'mtn_type', width: '4%'},
                    {data: 'mtn_status', name: 'mtn_status', className: 'dt-center', width:'7%',
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
                    {
                        data: 'id',
                        width: '1%',
                        orderable: false,
                        filterable: false,
                        searchable: false,
                        render: function(data, type, row) {
                          // let btn = 
                            // '<div class="btn-group" role="group"><button class="btn btn-info" onclick="return showData(\'' + data + '\');" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></button> ';
                          @can(session('menuId') . '_update')
                            let editPath = "{{ route('cat_maintenance.edit', ':id') }}";
                                editPath = editPath.replace(':id', data);

                            let btn = '<a href="' + editPath + '" class="btn btn-warning" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a>';

                                btn += 
                                      '<button onclick="return toggleState('+ data +');" class="btn btn-' + (row.mtn_status ? "danger":"primary") +'" data-toggle="tooltip" title="'+ (row.mtn_status ? "Disable" : "Enable") +'"><i class="fa '+ (row.mtn_status ? "fa-trash" : "fa-check") +'"></i></button>'
                          @endcan

                            btn += '</div>';

                            return btn;
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
            let url = "{{ route('cat_maintenance.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                    if (res.res) {
                        toastr.success('Category Maintenance berhasil diupdate', 'Success');
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
