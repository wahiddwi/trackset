@extends('adminlte::page')

@section('title', 'Selling')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Selling
                    <small>
                        Asset
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Selling Asset</li>
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
                <a href="{{ route('selling.create') }}" class="btn btn-primary">Add</a>
            @endcan
        </div>
        <br><br>

        <table id="selling_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tgl. Transaksi</th>
                    <th>Customer</th>
                    <th>Harga Jual</th>
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
          <h5 class="modal-title">Penjualan Asset</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <x-adminlte-input name="sell_num" label="Selling No." disabled fgroup-class="col-md-4" />
            <x-adminlte-input name="sell_date" label="Tanggal" disabled fgroup-class="col-md-4" />
            <x-adminlte-input name="sell_status" label="Status" disabled fgroup-class="col-md-4" />
          </div>

          <div class="row">
            <x-adminlte-input name="sell_cust_name" label="Customer" disabled fgroup-class="col-md-4" />
            <x-adminlte-input name="sell_cust_no" label="Identitas" disabled fgroup-class="col-md-4" />
            <x-adminlte-input name="sell_cust_telp" label="Tel" disabled fgroup-class="col-md-4" />
          </div>

          <div class="row">
            <x-adminlte-textarea name="sell_cust_addr" fgroup-class="col-md-12" class="form-control" label="Alamat"
              maxlength="255" disabled />
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
                  <th>Produk</th>
                  <th>Keterangan</th>
                  <th>Harga</th>
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
            tb = $('#selling_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('selling.index') }}",
                columns: [
                    {data: 'sell_no', name: 'sell_no', width: '4%'},
                    {data: 'sell_transdate', name: 'sell_transdate', className: 'dt-center', width: '5%', 
                      render: function (data) {
                        return moment(data).format('DD MMM yyyy')
                      }
                    },
                    {data: 'sell_cust_name', name: 'sell_cust_name', className: 'dt-center', width: '2%'},
                    {data: 'sell_total_price', name: 'sell_total_price', className: 'dt-center', width: '2%', 
                      render: function (data) {
                        return AutoNumeric.format(data, {
                          currencySymbol: 'Rp. ',
                          allowDecimalPadding: 'floats'
                        })
                      }
                    },
                    {data: 'sell_status', name: 'sell_status', className: 'dt-center', width:'7%',
                    render: function(data, type){
                        if(type === 'display'){
                            if(data == 'RSV') {
                                return '<span class="badge badge-primary">RSV</span>';
                            }
                            else if (data == 'ONHAND') {
                              return '<span class="badge badge-success">ONHAND</span>';
                            } else {
                                return '<span class="badge badge-danger">SELL</span>';
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
                          let btn =
                              '<div class="btn-group" role="group"><button class="btn btn-primary" onclick="return showData(\'' +
                              data + '\');" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></button>';

                          @can(session('menuId') . '_update')
                            let editPath = "{{ route('selling.edit', ':id') }}";
                            editPath = editPath.replace(':id', data);
                            let upd_disabled = row.sell_status == 'RSV' ? '' : 'd-none';

                            btn += '<a href="' + editPath + '" class="btn btn-primary ' + upd_disabled +
                              '" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a>';
                          @endcan
                          @can(session('menuId') . '_post')
                            let post_disabled = (row.sell_status != 'RSV') ? 'd-none' : '';
                            btn += '<button class="btn btn-success ' + post_disabled + '" onclick="return acceptRecord(\'' +
                              data + '\', \'' + row.sell_no +
                              '\');" data-toggle="tooltip" title="POST"><i class="fa fa-paper-plane"></i></button>';
                          @endcan

                          @can(session('menuId') . '_update')
                          let delete_disabled = row.sell_status == 'SELL' ? 'd-none' : '';
                        
                            btn += 
                                  '<button onclick="return toggleState('+ data +');" class="'+delete_disabled+' btn btn-' + (row.sell_status ? "danger":"primary") +'" data-toggle="tooltip" title="'+ (row.sell_status ? "Disable" : "Enable") +'"><i class="fa '+ (row.sell_status ? "fa-trash" : "fa-check") +'"></i></button>'
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

        // function removeRecord() {
        $('#removeRecord').on('click', function () {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Your file has been deleted.',
                        showConfirmButton: true,
                    });
                }
            });
        })

        // }

        function acceptRecord(id, no) {
          let url = "{{ route('selling.accept', ':id') }}";
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
                          toastr.success('Selling telah disetujui', 'Success');
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

          let showPath = "{{ route('selling.show', ':id') }}";
              showPath = showPath.replace(':id', id);

          $.ajax({
            type: "GET",
            url: showPath,
            success: function (res) {
              console.log(res.result);

              if (res.res) {
                $('#load-overlay').hide();

                $('#sell_num').val(res.result.sell_no);
                $('#sell_date').val(res.result.sell_transdate);
                $('#sell_status').val(res.result.sell_status);
                $('#sell_cust_name').val(res.result.sell_cust_name);
                $('#sell_cust_no').val(res.result.sell_cust_no);
                $('#sell_cust_telp').val(res.result.sell_cust_telp);
                $('#sell_cust_addr').val(res.result.sell_cust_addr);
                $('#remark').val(res.result.sell_desc);
                // $('#created_user').val(res.result.sell_created_name);

                $('#sell_dtl tbody').empty();

                res.result.detail.forEach(val => {  
                  let desc = val.dtl_sell_desc ? val.dtl_sell_desc : '';
                                  
                  let row = '<tr>' +
                    // '<td>' + val.ordering + '</td>' +
                    '<td>' + val.dtl_asset_transno + '</td>' +
                    '<td>' + val.dtl_asset_name + '</td>' +
                    '<td>' + desc + '</td>' +
                    '<td>'+ AutoNumeric.format(val.dtl_sell_price, {
                      currencySymbol: 'Rp. ',
                      allowDecimalPadding: 'floats'
                    }) + '</td>' +
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
            let url = "{{ route('selling.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                  
                    if (res.res) {
                        toastr.success('Penjualan berhasil diupdate', 'Success');
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
