@extends('adminlte::page')

@section('title', 'Master Lokasi')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Master
                    <small>
                        Lokasi
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Lokasi</li>
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
          <a href="{{ route('location.create') }}" class="btn btn-primary">Add</a>
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#showImportModal">
            <i class="fas fa-solid fa-upload"></i>
          </button>
          {{-- <button class="btn btn-primary" onclick="showModalImport()" data-toggle="tooltip" title="Import"><i class="fas fa-solid fa-upload"></i></button> --}}
        @endcan

      </div>
      <br><br>

        <table id="loc_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    {{-- <th>Cabang</th> --}}
                    <th>Lokasi</th>
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

<!-- Modal -->
<div class="modal fade" id="showImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload Location</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ route('loc.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-10">
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="file_upload" name="file">
                <label for="file_upload" class="custom-file-label">Upload Location</label>
              </div>
            </div>
            <div class="col-md-2 align-items-center">
              <a href="{{ route('loc.download') }}" class="btn btn-primary" data-toggle="tooltip"
              title="Download Template"><i class="fa-solid fa-file-export"></i></a>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <x-adminlte-button theme="primary" label="Upload" type="submit" icon="fa-solid fa-file-import" />
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
            tb = $('#loc_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('location.index') }}",
                columns: [
                    {data: 'loc_id', name: 'loc_id', width: '4%'},
                    // {data: 'loc.si_site', name: 'loc_site', width: '4%'},
                    {data: 'loc_name', name: 'loc_name', width: '4%'},
                    {data: 'loc_active', name: 'loc_active', className: 'dt-center', width:'7%',
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
                                '<a href="{{ url('location') }}/' + data +
                                '/edit" class="btn btn-secondary ' + disabled +
                                '" data-toggle="tooltip" title="View"><i class="fa fa-pen"></i></a>' +
                                '<button onclick="return toggleState(' + data + ');" class="btn btn-' + (row
                                    .loc_active ? "danger" : "primary") + disabled +
                                '" data-toggle="tooltip" title="' + (row.loc_active ? "Disable" :
                                    "Enable") + '"><i class="fa ' + (row.loc_active ? "fa-trash" :
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

        function showModalImport() {
          $('#showImportModal').modal('show');
        }

        function toggleState(id) {
            let url = "{{ route('location.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                    if (res.res) {
                        toastr.success('Lokasi berhasil diupdate', 'Success');
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

        $('#btnImport').on('click', function () {
          $('#load-overlay-imp').attr('hidden', false);
        });
    </script>
@stop
