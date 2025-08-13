@extends('adminlte::page')

@section('title', 'Disposal')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Disposal
                    <small>
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Disposal</li>
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
                <a href="{{ route('disposal.create') }}" class="btn btn-primary">Add</a>
            @endcan
        </div>
        <br><br>

        <table id="disposal_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tgl. Transaksi</th>
                    <th>Keterangan</th>
                    <th>Satus</th>
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
          <h5 class="modal-title">Disposal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <x-adminlte-input name="dis_transno" label="Disposal No." disabled fgroup-class="col-md-6" />
            <x-adminlte-input name="dis_transdate" label="Tanggal" disabled fgroup-class="col-md-6" />
          </div>

          <div class="row">
            <x-adminlte-input name="dis_company" label="Company" disabled fgroup-class="col-md-6" />
            <x-adminlte-input name="dis_status" label="Status" disabled fgroup-class="col-md-6" />
          </div>

          <div class="row">
            <x-adminlte-textarea name="remark" fgroup-class="col-md-12" class="form-control" label="Keterangan"
              maxlength="255" disabled />
          </div>

          <div class="row">
            <table class="table table-bordered responsive nowrap" id="sell_dtl">
              <thead>
                <tr>
                  <th>No</th>
                  <th>No. Transaksi</th>
                  <th>Cabang</th>
                  <th>Produk</th>
                  {{-- <th>&nbsp;</th> --}}
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>

          {{-- <div class="row col-md-12">
            <small>
              Dibuat oleh <span id="created_user"></span>
            </small>
          </div> --}}
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
            tb = $('#disposal_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                  [3, 'asc'],
                  [1, 'desc']
                ],
                ajax: "{{ route('disposal.index') }}",
                columns: [
                    {data: 'dis_transno', name: 'dis_transno', width: '4%'},
                    {data: 'dis_transdate', name: 'dis_transdate', className: 'dt-center', width: '5%', 
                      render: function (data) {
                        return moment(data).format('DD MMM yyyy')
                      }
                    },
                    {data: 'dis_desc', name: 'dis_desc', className: 'dt-center', width: '2%'},
                    {data: 'dis_status', name: 'dis_status', className: 'dt-center', width: '2%', 
                      render: function (data, type) {
                        if(data == "DISPOSAL") {
                            return '<span class="badge badge-success">DISPOSAL</span>';
                          }
                          else if (data == 'RSV') {
                              return '<span class="badge badge-primary">RSV</span>';
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
                          let btn =
                              '<div class="btn-group" role="group"><button class="btn btn-primary" onclick="return showData(\'' +
                              data + '\');" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></button>';

                          @can(session('menuId') . '_update')
                            let editPath = "{{ route('disposal.edit', ':id') }}";
                            editPath = editPath.replace(':id', data);
                            let upd_disabled = row.dis_status == 'RSV' ? '' : 'd-none';

                            btn += '<a href="' + editPath + '" class="btn btn-warning ' + upd_disabled +
                              '" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a>';
                          @endcan
                          @can(session('menuId') . '_post')
                            let post_disabled = (row.dis_status != 'RSV') ? 'd-none' : '';
                            btn += '<button class="btn btn-success ' + post_disabled + '" onclick="return acceptRecord(\'' +
                              data + '\', \'' + row.dis_transno +
                              '\');" data-toggle="tooltip" title="POST"><i class="fa fa-paper-plane"></i></button>';
                          @endcan

                          @can(session('menuId') . '_update')
                          let delete_disabled = (row.dis_status == 'CANCEL' || row.dis_status == 'DISPOSAL') ? 'd-none' : '';
                        
                            btn += 
                                  '<button onclick="return toggleState('+ data +');" class="'+delete_disabled+' btn btn-' + (row.dis_status ? "danger":"primary") +'" data-toggle="tooltip" title="'+ (row.dis_status ? "Disable" : "Enable") +'"><i class="fa '+ (row.dis_status ? "fa-trash" : "fa-check") +'"></i></button>'
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
          let url = "{{ route('disposal.accept', ':id') }}";
              url = url.replace(':id', id);
          
            Swal.fire({
                title: 'Are you sure approved '+ no +'?',
                text: "You won't be able to modify or delete data once it's approved.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                          "id": id,
                          "_token": "{{ csrf_token() }}",
                        },
                        dataType: "json",
                        success: function (res) {
                          toastr.success('Disposal telah disetujui', 'Success');
                          tb.fnDraw();
                        },
                        error: function (err) {
                            toastr.error('Mohon coba sesaat lagi', 'Error');
                            console.log(err);
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

          let showPath = "{{ route('disposal.show', ':id') }}";
              showPath = showPath.replace(':id', id);

          $.ajax({
            type: "GET",
            url: showPath,
            success: function (res) {

              if (res.res) {
                $('#load-overlay').hide();
                
                $('#dis_transno').val(res.result.dis_transno);
                $('#dis_transdate').val(moment(res.result.dis_transdate).format('DD MMM yyyy'));
                $('#dis_company').val(res.result.company.co_name);
                // $('#dis_site').val(res.result.site.si_name);
                $('#dis_status').val(res.result.dis_status);
                $('#remark').val(res.result.dis_desc);
                $('#created_user').val(res.result.sell_created_name);

                $('#sell_dtl tbody').empty();                

                res.result.detail.forEach(val => {     
                  console.log(val);
                               
                  let row = '<tr>' +
                    '<td>' + val.disdtl_order + '</td>' +
                    '<td>' + val.disdtl_asset_transno + '</td>' +
                    '<td>' + val.site.si_name + '</td>' +
                    '<td>' + val.disdtl_asset_name + '</td>' +
                  '</tr>';
                  $('#sell_dtl tbody').append(row);

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

        function toggleState(id) {
          let url = "{{ route('disposal.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                  
                    if (res.res) {
                        toastr.success('Disposal berhasil diupdate', 'Success');
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
