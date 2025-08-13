@extends('adminlte::page')

@section('title', 'Asset Request')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Asset Request
                        <small>
                            <span class="badge badge-primary">{{ $count }}</span>
                        </small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Asset Request</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
          {{-- @can($menuId . '_post')
            <a href="{{ route('sites.sync') }}" class="btn btn-primary">Sync</a> <br><br>
          @endcan --}}
            <table id="asset_request_table" class="table table-bordered display responsive nowrap" style="width: 100%">
              <thead>
                <tr>
                  <th>No. SPB</th>
                  <th>Tgl. Transaksi</th>
                  <th>Cabang</th>
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
          <h5 class="modal-title">Asset Request Detail</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <x-adminlte-input name="req_spb" label="SPB No." disabled fgroup-class="col-md-6" />
            <x-adminlte-input name="req_transdate" label="Tanggal" disabled fgroup-class="col-md-6" />
          </div>

          <div class="row">
            <x-adminlte-input name="req_site" label="Cabang" disabled fgroup-class="col-md-6" />
            {{-- <x-adminlte-input name="dis_site" label="Cabang" disabled fgroup-class="col-md-4" /> --}}
            <x-adminlte-input name="req_status" label="Status" disabled fgroup-class="col-md-6" />
          </div>

          {{-- <div class="row">
            <x-adminlte-textarea name="remark" fgroup-class="col-md-12" class="form-control" label="Keterangan"
              maxlength="255" disabled />
          </div> --}}

          <div class="row">
            <table class="table table-bordered responsive nowrap" id="sell_dtl">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Item</th>
                  <th>Permintaan</th>
                  <th>Transfer</th>
                  <th>Beli</th>
                  {{-- <th>Terima</th> --}}
                </tr>
              </thead>
              <tbody>
              </tbody>
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

  <!-- Modal Purchase -->
  <div class="modal fade" id="modal-purchase" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Beli Asset</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="{{ route('asset_request.purchase_process') }}" method="POST" id="formPurchase">
            <div class="row">
              <input type="hidden" name="req_id" id="req_id">
              <input type="hidden" name="line_count" id="line_count">
              <input type="hidden" name="req_spb" id="spb">
              <input type="hidden" name="req_transdate" id="transdate">
              <x-adminlte-input name="purchase_spb" id="purchase_spb" label="SPB No." disabled fgroup-class="col-md-6" />
              <x-adminlte-input name="purchase_transdate" id="purchase_transdate" label="Tanggal" disabled fgroup-class="col-md-6" />
            </div>

            <div class="card">
              <table class="table table-bordered responsive nowrap" id="purchase_table">
                <thead>
                  <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Permintaan</th>
                    <th class="text-center">Transfer</th>
                    <th class="text-center">Beli</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

            <div class="btn-group">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-success" id="btn_beli">Beli</button>
            </div>
          </form>
        </div>

        <div class="overlay" id="load-overlay-pur">
          <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
      </div>
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
        function setDatatable() {
            let tb = $('#asset_request_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                    [3, 'asc'],
                    [1, 'desc']
                ],
                ajax: "{{ route('asset_request.index') }}",
                columns: [
                    {data: 'req_spb', name: 'req_spb', width: '4%'},
                    {data: 'req_transdate', name: 'req_transdate', className: 'dt-center', width: '5%', 
                      render: function (data) {
                        return moment(data).format('DD MMM yyyy')
                      }
                    },
                    {data: 'req_site', name: 'req_site', className: 'dt-center', width: '2%'},
                    {data: 'req_status', name: 'req_status', className: 'dt-center', width: '2%', 
                      render: function (data, type) {                        
                        if(data == "CLOSE") {
                            return '<span class="badge badge-success">CLOSE</span>';
                          }
                        else if (data == 'OPEN') {
                            return '<span class="badge badge-primary">OPEN</span>';
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
                            let closePath = "{{ route('asset_request.close_spb', ':id') }}";
                                closePath = closePath.replace(':id', data);
                            let disabled = (row.req_status == "OPEN") ? "" : "d-none";
                                btn += `<a href="${closePath}" class="btn btn-danger ${disabled}" data-toggle="tooltip" 
                                    title="CLOSE"><i class="fa-solid fa-paper-plane"></i></a>`;

                                btn += `<button class="btn btn-success ${disabled}" onclick="return showPurchase('${data}');" 
                                        data-toggle="tooltip" title="BELI"><i class="fa fa-solid fa-store"></i></button>`;
                          @endcan
                          
                          btn += '</div>';

                          return btn;
                        }
                    },
                ],
                search: {
                  regex: true
                },
                oLanguage: {
                    sProcessing: '<span class="info-box-icon">' +
                        '<img src="{{ asset('assets/img/RGLogo.png') }}" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" width="200" alt="">' +
                        '</span>' +
                        '<div class="info-box-content">' +
                        '<span class="info-box-text">Loading</span>' +
                        '</div>'
                }
            });
        }

        $(function() {
            setDatatable();
        });

        function showData(id) {
          $('#load-overlay').show();
          $('#modal-detail').modal('show');
          $('#modal-detail input').val(null);
          $('#modal-detail textarea').val(null);
          $('#modal-detail span').val(null);

          let showPath = "{{ route('asset_request.show', ':id') }}";
              showPath = showPath.replace(':id', id);

          $.ajax({
            type: "GET",
            url: showPath,
            success: function (res) {

              if (res.res) {
                $('#load-overlay').hide();
                
                $('#req_spb').val(res.result.req_spb);
                $('#req_transdate').val(moment(res.result.req_transdate).format('DD MMM yyyy'));
                $('#req_site').val(res.result.site.si_name);
                $('#req_status').val(res.result.req_status);

                $('#sell_dtl tbody').empty();                

                res.result.detail.forEach(val => {     
                  let row = '<tr>' +
                    '<td>' + val.reqdtl_line + '</td>' +
                    '<td>' + val.reqdtl_item + '</td>' +
                    '<td>' + val.reqdtl_qty_approve + '</td>' +
                    '<td>' + val.reqdtl_qty_send + '</td>' +
                    '<td>' + val.reqdtl_qty_purchase + '</td>' +
                    // '<td>' + val.reqdtl_qty + '</td>' +
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

        function showPurchase(id) {
          $('#load-overlay-pur').show();
          $('#modal-purchase').modal('show');
          $('#modal-purchase input').val(null);
          $('#modal-purchase textarea').val(null);
          $('#modal-purchase span').val(null);

          let purchasePath = "{{ route('asset_request.purchase', ':id') }}";
              purchasePath = purchasePath.replace(':id', id);

          $.ajax({
            type: "GET",
            url: purchasePath,
            success: function (res) {
              if (res.res) {
                $('#load-overlay-pur').hide();
                
                $('#req_id').val(res.data.id);
                $('#line_count').val(res.data.req_line);
                $('#purchase_spb').val(res.data.req_spb);
                $('#purchase_transdate').val(moment(res.data.req_transdate).format('DD MMM yyyy'));
                $('#spb').val(res.data.req_spb);
                $('#transdate').val(moment(res.data.req_transdate).format('DD MMM yyyy'));
                

                $('#purchase_table tbody').empty();

                res.data.detail.forEach((val, index) => {
                  // console.log(val);
                  
                  let rowNumber = index + 1;
                  let qtyPurchase = (val.reqdtl_qty_approve - val.reqdtl_qty_send);

                  let row = `
                    <tr data-row="${rowNumber}">
                      <td class="text-center align-middle">
                        <span class="index">${rowNumber}</span>
                        <input type="hidden" name="trfdtl_order[]" value="${val.reqdtl_line}">
                        <input type="hidden" name="trfdtl_id[]" value="${val.reqdtl_id}">
                      </td>
                      <td class="align-middle">
                        ${val.reqdtl_item}
                        <input type="hidden" name="trfdtl_itemname[]" value="${val.reqdtl_item}">
                        <input type="hidden" name="trfdtl_code[]" value="${val.reqdtl_code}">
                        <input type="hidden" name="trfdtl_uom[]" value="${val.reqdtl_uom}">
                        <input type="hidden" name="trfdtl_qty[]" value="${val.reqdtl_qty}">
                      </td>
                      <td class="text-center align-middle">
                        ${val.reqdtl_qty_approve}
                        <input type="hidden" name="trfdtl_qty_approve[]" value="${val.reqdtl_qty_approve}">
                      </td>
                      <td class="text-center align-middle">
                        ${val.reqdtl_qty_send}
                        <input type="hidden" name="trfdtl_qty_send[]" value="${val.reqdtl_qty_send}">
                      </td>
                      <td class="text-center align-middle">
                        ${qtyPurchase}
                        <input type="hidden" name="trfdtl_qty_purchase[]" value="${qtyPurchase}">
                      </td>
                    </tr>
                  `;

                  $('#purchase_table tbody').append(row);
                });
              } else {
                $('#modal-purchase input').val(null);
                $('#modal-purchase textarea').html(null);
                $('#modal-purchase span').html(null);

                $('#modal-purchase').modal('hide');
                toastr.error('mohon coba sesaat lagi.', 'Error');
              }
            },
            error: function (xhr, ajaxOptions, thrownError) {
              $('#modal-purchase').modal('hide');
              toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
            }
          });
        }

        $('#btn_beli').on('click', function (e) {
          e.preventDefault();
          
          let formData = new FormData($('#formPurchase')[0]);
          formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
          
          $.ajax({
            url: $('#formPurchase').attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
              // Handle success
              if (res.res) {
                $('#modal-purchase').modal('hide');
                toastr.success('Berhasil membeli asset.', 'Success');
                $('#asset_request_table').DataTable().ajax.reload(null, false);
              }
            },
            error: function(xhr) {
              // Handle error
            }
          });
        });
    </script>
@stop
