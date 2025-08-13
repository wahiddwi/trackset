@extends('adminlte::page')

@section('title', 'Maintenance')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Maintenance
                    <small>
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Maintenance</li>
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
                <a href="{{ route('maintenance.create') }}" class="btn btn-primary">Add</a>
            @endcan
        </div>
        <br><br>

        <table id="maintenance_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tgl. Transaksi</th>
                    <th>Vendor</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

  <!-- Modal -->
  <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Maintenance</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <x-adminlte-input name="main_transno" label="Maintenance No." disabled fgroup-class="col-md-3" />
            <x-adminlte-input name="main_transdate" label="Tanggal" disabled fgroup-class="col-md-3 " />
            <x-adminlte-input name="main_company" label="Company" disabled fgroup-class="col-md-4" />
            <x-adminlte-input name="main_status" label="Status" disabled fgroup-class="col-md-2" />
          </div>

          <div class="row">
            <table class="table table-striped table-bordered responsive nowrap" id="maintenance_dtl">
              <thead>
                <tr>
                  <th>No</th>
                  <th>No. Transaksi</th>
                  <th>Asset</th>
                  <th>Keterangan</th>
                  <th>Biaya</th>
                  <th>Jumlah Service</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="5" class="text-right">Total</th>
                  <th class="text-right"><span id="totalCost">Rp. 0</span></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

        <div class="overlay" id="load-overlay">
          <i class="fas fa-2x fa-sync-alt fa-spin"></i>
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

        .dt-center {
            text-align: center;
          }
          #maintenance_dtl tfoot th:last-child {
    border: none !important;
}

    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>

    <script>
        let tb;
        let approved = '{{ auth()->user()->can($menuId . '_post') }}';
        function setDatatable(){
            tb = $('#maintenance_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                  [4, 'asc'],
                  [1, 'desc']
                ],
                ajax: "{{ route('maintenance.index') }}",
                columns: [
                    {data: 'main_transno', name: 'main_transno', width: '4%'},
                    {data: 'main_transdate', name: 'main_transdate', width: '5%', 
                      render: function (data) {
                        return moment(data).format('DD MMM yyyy')
                      }
                    },
                    {data: 'main_vendor', name: 'vendor.vdr_name', width: '2%'},
                    {data: 'main_total_cost', name: 'main_total_cost', width: '2%', 
                      render: function (data) {
                        return AutoNumeric.format(data, {
                          currencySymbol: 'Rp. ',
                          allowDecimalPadding: 'floats'
                        });
                      }
                    },
                    {data: 'main_status', name: 'main_status', width: '2%', 
                      render: function (data, type) {
                        if(data == "DRAFT") {
                            return '<span class="badge badge-primary">DRAFT</span>';
                          }
                          else if (data == 'POST') {
                              return '<span class="badge badge-success">POST</span>';
                          } 
                          else {
                              return '<span class="badge badge-danger">CANCEL</span>';
                          }
                        return data;
                      }
                    },
                    {
                        data: 'id',
                        width: '1%',
                        orderable: false,
                        filterable: false,
                        searchable: false,
                        render: function(data, type, row) {
                          let showPath = "{{ route('maintenance.show', ':id') }}";
                              showPath = showPath.replace(':id', data); 
                          let btn = `<div class="btn-group" role="group">
                            <a href="${showPath}" class="btn btn-primary" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></a>`;
                            // </div>`;

                          @can(session('menuId') . '_update')
                            let editPath = "{{ route('maintenance.edit', ':id') }}";
                            editPath = editPath.replace(':id', data);
                            let upd_disabled = row.main_status != 'DRAFT' ? 'd-none' : '';

                            btn += '<a href="' + editPath + '" class="btn btn-warning ' + upd_disabled +
                              '" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a>';
                          @endcan
                          @can(session('menuId') . '_post')
                            let post_disabled = (row.main_status != 'DRAFT') ? 'd-none' : '';
                            btn += '<button class="btn btn-success ' + post_disabled + '" onclick="return acceptRecord(\'' +
                              data + '\', \'' + row.main_transno +
                              '\');" data-toggle="tooltip" title="POST"><i class="fa fa-paper-plane"></i></button>';
                              
                          @endcan
                          @can(session('menuId') . '_update')
                            let delete_disabled = (row.main_status != 'DRAFT') ? 'd-none' : '';                       
                            btn += 
                                  '<button onclick="return toggleState(\'' +
                              data + '\', \'' + row.main_transno +
                              '\');" class="'+delete_disabled+' btn btn-danger" data-toggle="tooltip" title="DELETE"><i class="fa fa-trash"></i></button>'
                          @endcan
                          @can(session('menuId') . '_print')
                            let printPath = "{{ route('maintenance.print', ':id') }}";
                                printPath = printPath.replace(':id', data);
                            let print_disabled = (row.main_status != 'POST') ? 'd-none' : '';

                            btn += '<a href="' + printPath + '" class="btn btn-info ' + print_disabled + '" data-toggle="tooltip" target="_blank" title="PRINT"><i class="fa fa-print"></i></a>';
                          @endcan
                          btn += '</div>';
                          return btn;
                        }
                    },
                ],
                search: {
                    "regex": true
                },
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

        function acceptRecord(id, no) {
          let url = "{{ route('maintenance.post', ':id') }}";
              url = url.replace(':id', id);
          
            Swal.fire({
                title: `Apakah Anda yakin ingin menyetujui ${no}?`,
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
                  title: 'APPROVED!',
                  text: 'Data berhasil diposting.',
                  showConfirmButton: true,
                }).then((result) => {
                  if (result.isConfirmed) {
                    $.ajax({
                      type: "POST",
                      url: url,
                      data: {
                        "_token": "{{ csrf_token() }}",
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
                        toastr.error('Mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
                      }
                    });
                  }
                });
              }
            });
        }

        function showData(id) {
          $('#load-overlay').show();
          $('#modal-detail').modal('show');
          $('#modal-detail input').val(null);
          $('#modal-detail textarea').val(null);
          $('#modal-detail span').val(null);

          let showPath = "{{ route('maintenance.show', ':id') }}";
              showPath = showPath.replace(':id', id);

          $.ajax({
            type: "GET",
            url: showPath,
            success: function (res) {

              if (res.res) {
                $('#load-overlay').hide();

                $('#main_transno').val(res.result.main_transno);
                $('#main_transdate').val(moment(res.result.main_transdate).format('DD MMM yyyy'));
                $('#main_company').val(res.result.company.co_name);
                $('#main_status').val(res.result.main_status);
                $('#totalCost').html(AutoNumeric.format(res.result.main_total_cost, {
                  currencySymbol: 'Rp. ',
                  allowDecimalPadding: 'floats'
                }));

                $('#maintenance_dtl tbody').empty();                

                res.result.detail.forEach(val => {     
                  let row = '<tr>' +
                    '<td>' + val.maindtl_line + '</td>' +
                    '<td>' + val.maindtl_asset_transno + '</td>' +
                    '<td>' + val.maindtl_asset_name + '</td>' +
                    '<td>' + val.maindtl_desc + '</td>' +
                    '<td>' + AutoNumeric.format(val.maindtl_cost, {
                      currencySymbol: 'Rp. ',
                      allowDecimalPadding: 'floats'
                    }) + '</td>' +
                    '<td>' + val.maindtl_counter + '</td>' +
                  '</tr>';
                  $('#maintenance_dtl tbody').append(row);

                });
                
              } else {
                $('#modal-detail input').val(null);
                $('#modal-detail textarea').html(null);
                $('#modal-detail span').html(null);

                $('#modal-detail').modal('hide');
                toastr.error('mohon coba sesaat lagi.', 'Error');
              }
            },
            error: function (xhr, ajaxOptions, thrownError) {
              $('#modal-detail').modal('hide');
              toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
            }
          });
          
        }

        function toggleState(id, no) {
          let url = "{{ route('maintenance.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            Swal.fire({
                title: `Apakah Anda yakin ingin menghapus ${no}?`,
                text: "Setelah dihapus, Anda tidak akan dapat mengubah data tersebut.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
              if (result.isConfirmed) {
                Swal.fire({
                  icon: 'success',
                  title: 'APPROVED!',
                  text: 'Data berhasil dihapus.',
                  showConfirmButton: true,
                }).then((result) => {
                  if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(res) {
                          
                            if (res.res) {
                                toastr.success(res.msg, 'Success');
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
                });
              }
            });
        }

    </script>
@stop
