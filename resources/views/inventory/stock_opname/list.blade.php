@extends('adminlte::page')

@section('title', 'Stock Opname')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Stock Opname
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Stock Opname</li>
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
                <a href="{{ route('stock_opname.create') }}" class="btn btn-primary">Add</a> <br><br>
            @endcan

        <table id="stock_opname_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tgl. Transaksi</th>
                    <th>Cabang</th>
                    <th>Lokasi</th>
                    <th>Status</th>
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
@section('plugins.Sweetalert2', true)
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
            tb = $('#stock_opname_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                searchable: true,
                order: [
                  [4, 'asc'],
                  [0, 'desc'],
                ],
                ajax: "{{ route('stock_opname.index') }}",
                columns: [
                    {data: 'stock_transno', name: 'stock_transno', width: '4%'},
                    {data: 'stock_transdate', name: 'stock_transdate', width: '4%', 
                      render: function (data) {
                        return moment(data).format('DD MMM yyyy')
                      }
                    },
                    {data: 'stock_site_name', name: 'stock_site_name', width: '4%'},
                    {data: 'stock_loc_name', name: 'stock_loc_name', width: '4%'},
                    {data: 'stock_status', name: 'stock_status', className: 'dt-center', width:'7%',
                    render: function(data, type, row){
                        if(type === 'display'){
                            if(data == 'OPEN') {
                                return '<span class="badge badge-primary">OPEN</span>';
                            } else if(data == 'CLOSE') {
                              return '<span class="badge badge-success">CLOSE</span>';
                            } else {
                                return '<span class="badge badge-danger">CANCEL</span>';
                            }
                        }
                        return data;
                    }},
                    {
                        data: 'id',
                        width: '1%',
                        orderable: false,
                        filterable: false,
                        searchable: false,
                        render: function(data, type, row) {
                        let displayPath = "{{ route('stock_opname.show', ':id') }}";
                            displayPath = displayPath.replace(':id', data);

                        let btn = `
                                  <div class="btn-group" role="group"><a href="${displayPath}" class="btn btn-primary" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></a>
                        `;

                          @can($menuId . '_update')
                            let editPath = "{{ route('stock_opname.edit', ':id') }}";
                                editPath = editPath.replace(':id', data);
                            let editDisabled = row.stock_status == 'OPEN' ? '':'d-none';

                              btn += 
                                      '<div class="btn-group" role="group"><a href="'+ editPath +'" class="btn btn-warning '+ editDisabled +'" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a>';
                              // btn += 
                              //         '<div class="btn-group" role="group"><a href="'+ editPath +'" class="btn btn-warning '+ editDisabled +'" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a>';
                          @endcan

                          @can($menuId . '_post')
                              let postDisabled = row.stock_status == 'OPEN' ? '' : 'd-none';
                              btn += '<button class="btn btn-success '+ postDisabled +'" onclick="return acceptRecord(\'' +
                                data + '\', \'' + row.stock_transno +
                                '\');" data-toggle="tooltip" title="POST"><i class="fa fa-paper-plane"></i></button>';
                          @endcan
                                      
                          @can(session('menuId') . '_delete')
                            let deleteDisabled = row.stock_status == 'OPEN' ? '':'d-none';
                                btn += '<button class="btn btn-danger '+ deleteDisabled +'" data-toggle="tooltip" title="REJECT" onclick="return toggleReject('+ data +')"><i class="fa-solid fa-ban"></i></button>';
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

        function acceptRecord(id, transno) {
          let postPath = "{{ route('stock.accept', ':id') }}";
          postPath = postPath.replace(':id', id);
          
          Swal.fire({
              title: 'Apakah Anda yakin ingin menyetujui ini?',
              text: "Setelah disetujui, Anda tidak akan dapat mengubah atau menghapus data tersebut.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes!'
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire({
                icon: 'success',
                title: 'APPROVE!',
                text: 'Data berhasil diposting.',
                showConfirmButton: true,
              }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    type: "POST",
                    url: postPath,
                    data: {
                      _token: "{{ csrf_token() }}",
                    },
                    success: function (res) {
                      if (res.res) {
                        toastr.success(res.msg, 'Success');
                        tb.fnDraw();
                      } else {
                        toastr.error(res.msg, 'Error');
                      }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                      toastr.error('Mohon coba sesaat lagi. ('+ xhr.status +')', 'Error');
                    }
                  });
                }
              });
            }
          });
        }


        function toggleReject(id) {
          let deletePath = "{{ route('stock.reject', ':id') }}";
              deletePath = deletePath.replace(':id', id);

          Swal.fire({
            title: 'Apakah Anda yakin ingin menolak ini?',
            text: "Setelah ditolak, Anda tidak akan dapat mengubah atau menghapus data tersebut.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes!'
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                type: "POST",
                url: deletePath,
                data: {
                  _token: "{{ csrf_token() }}",
                },
                success: function (res) {
                  if (res.res) {
                    toastr.success('Stock Opname berhasil direject.', 'Success');
                    tb.fnDraw();
                  } else {
                    toastr.error('Stock Opname Gagal direject.', 'Error');
                  }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                  toastr.error('Mohon coba sesaat lagi. ('+ xhr.status +')', 'Error');
                }
              });
            }
          });
        }
    </script>
@stop
