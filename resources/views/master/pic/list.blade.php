@extends('adminlte::page')

@section('title', 'Master PIC')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Master
                    <small>
                        PIC
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master PIC</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
      <div style="display: inline;">
        @can($menuId . '_create')
            <a href="{{ route('pic.create') }}" class="btn btn-primary">Add</a>
            <button class="btn btn-primary" onclick="showModalImport();" data-toggle="tooltip" title="Import"><i class="fas fa-solid fa-upload"></i></button>
        @endcan 
      </div>
      <br><br>

        <table id="pic_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
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

<div class="modal fade" id="showModalImport" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import PIC</h5>
                {{-- @can($menuId . '_print')
                    <div class="btn-group">
                        <a href="" id="printDetail" class="btn btn-warning" target="_blank"><i class="fa fa-print"></i> Print</a>
                    </div>
                @endcan --}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="detailClose();">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('pic.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                      <div class="col-md-10">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="file_upload" name="file">
                          <label class="custom-file-label" for="file_upload">Upload PIC</label>
                        </div>
                      </div>
                      <div class="col-md-2 align-items-center">
                        <a href="{{ route('pic.download') }}" class="btn btn-primary" data-toggle="tooltip"
                          title="Download Template"><i class="fa-solid fa-file-export"></i></a>
                      </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <x-adminlte-button theme="primary" label="Import" type="submit" icon="fas fa-lg fa-save" />
                </div>
            </form>
        </div>
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
            tb = $('#pic_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('pic.index') }}",
                columns: [
                    {data: 'pic_nik', name: 'pic_nik', width: '4%'},
                    // {data: 'loc.si_site', name: 'loc_site', width: '4%'},
                    {data: 'pic_name', name: 'pic_name', width: '4%'},
                    {data: 'pic_status', name: 'pic_status', className: 'dt-center', width:'7%',
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
                            let disabled = '{{ auth()->user()->can($menuId . '_update') }}' ? '' : 'd-none';
                            return '<div class="btn-group">' +
                                '<a href="{{ url('pic') }}/' + data +
                                '/edit" class="btn btn-secondary ' + disabled +
                                '" data-toggle="tooltip" title="View"><i class="fa fa-pen"></i></a>' +
                                '<button onclick="return toggleState(' + data + ');" class="btn btn-' + (row
                                    .pic_status ? "danger" : "primary") + disabled +
                                '" data-toggle="tooltip" title="' + (row.pic_status ? "Disable" :
                                    "Enable") + '"><i class="fa ' + (row.pic_status ? "fa-trash" :
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
            let url = "{{ route('pic.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                    if (res.res) {
                        toastr.success('PIC berhasil diupdate', 'Success');
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

        function showModalImport() {
          $('#showModalImport').modal('show');
        }
    </script>
@stop
