@extends('adminlte::page')

@section('title', 'Stock Opname - Edit')

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
                    <li class="breadcrumb-item"><a href="{{route('stock_opname.index')}}">Stock Opname</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
      <div class="row">
        <x-adminlte-input name="stock_transno" id="stock_transno" label="No. Transaksi" placeholder="AUTONUMBER" maxlength="60"
        fgroup-class="col-md-6" error-key="stock_transno" value="{{ $stock->stock_transno }}" disabled/>
        <input type="hidden" id="stockId" name="stock_id" value="{{ $stock->id }}">

        @php
          $config = [
              'format' => 'DD MMM YYYY',
              'dayViewHeaderFormat' => 'MMM YYYY',
          ];
        @endphp
        <x-adminlte-input-date name="stock_transdate" label="Tgl. Perolehan" igroup-size="md" error-key="inv_obtaindate"
          fgroup-class="col-md-6" :config="$config" value="{{ date('d-m-Y', strtotime($stock->stock_transdate)) }}" disabled>
          {{-- fgroup-class="col-md-6" :config="$config" value="{{ $stock->stock_transdate->format('d-m-Y') }}" enable-old-support disabled> --}}
          <x-slot name="appendSlot">
            <div class="input-group-text bg-dark">
              <i class="fas fa-calendar-day"></i>
            </div>
          </x-slot>
        </x-adminlte-input-date>
      </div>
      <div class="row">
        <x-adminlte-select2 name="stock_site" id="site" label="Cabang" label-class="must-fill" fgroup-class="col-md-6" error-key="stock_site" class="form-control" disabled>
          <option value="{{ $stock->stock_site }}" selected disabled>{{ $stock->stock_site .' - '. $stock->site->si_name }}</option>
        </x-adminlte-select2>
        
        <x-adminlte-select2 name="stock_loc" id="location" label="Lokasi" label-class="must-fill" fgroup-class="col-md-6" error-key="stock_loc" class="form-control" disabled>
          <option value="{{ $stock->stock_loc }}" selected disabled>{{ $stock->loc->loc_name }}</option>
        </x-adminlte-select2>
      </div>
      <div class="row">
        <x-adminlte-textarea name="stock_desc" label="Keterangan" fgroup-class="col-md-12" rows="5" igroup-size="sm" placeholder="Keterangan..." disabled>
          {{ $stock->stock_desc }}
        </x-adminlte-textarea>
      </div>
      <hr>
      {{-- @dd($stock->stock_itemttl); --}}
      <div class="row mt-3">
        <x-adminlte-input name="stock_itemttl" id="stock_itemttl" label="Jumlah Item Keseluruhan" maxlength="60"
        fgroup-class="col-md-6" error-key="stock_itemttl" value="{{ $stock->stock_itemttl }}" disabled/>

        <x-adminlte-input name="stock_additional" id="stock_additional" label="Jumlah Item Tambahan" placeholder="" maxlength="60"
        fgroup-class="col-md-6" error-key="stock_additional" value="{{ $stock->stock_additional }}" disabled/>
      </div>
      <div class="row">
        <x-adminlte-input name="stock_found" id="stock_found" label="Jumlah Item Ditemukan" placeholder="" maxlength="60"
        fgroup-class="col-md-6" error-key="stock_found" value="{{ $stock->stock_found }}" disabled/>

        <x-adminlte-input name="stock_opname" id="stock_opname" label="Jumlah Item tidak ditemukan" placeholder="" maxlength="60"
        fgroup-class="col-md-6" error-key="stock_opname" value="{{ $stock->stock_opname }}" disabled/>
      </div>
      <hr>
      <div class="row mt-3">
        <x-adminlte-input name="stock_scan" label="Scan No. Asset" placeholder="Masukan No. Asset untuk scan data..." maxlength="60"
        fgroup-class="col-md-6" igroup-size="lg" disable-feedback value="" id="stock_scan"/>
        
      </div>
    </div>
    <div class="overlay" id="load-overlay-scan" hidden>
      <i class="fas fa-2x fa-sync-alt fa-spin"></i>
    </div>
</div>

<div class="card">
  <form action="{{ route('stock_opname.update', $stock->id) }}" method="POST" id="formAccept">
    @csrf
    @method('PUT')
    <div class="card-body">
      <!-- Nav tabs -->
      <input type="hidden" id="stockId" name="stock_id" value="{{ $stock->id }}">
      <input type="hidden" id="stock_transno" name="stock_transno" value="{{ $stock->stock_transno }}">
      <input type="hidden" id="stock_transdate" name="stock_transdate" value="{{ $stock->stock_transdate }}">
      <input type="hidden" id="stock_site" name="stock_site" value="{{ $stock->stock_site }}">
      <input type="hidden" id="stock_loc" name="stock_loc" value="{{ $stock->stock_loc }}">
      <input type="hidden" id="stock_desc" name="stock_desc" value="{{ $stock->stock_desc }}">
      <input type="hidden" id="stock_opname" name="stock_opname" value="{{ $stock->stock_opname }}">
      <input type="hidden" id="stock_additional" name="stock_additional" value="{{ $stock->stock_additional }}">
      <input type="hidden" id="stock_found" name="stock_found" value="{{ $stock->stock_found }}">
      <input type="hidden" id="stock_itemttl" name="stock_itemttl" value="{{ $stock->stock_itemttl }}">
      <div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="item-tab" data-toggle="tab" href="#tab-item-data" role="tab" aria-controls="emp" aria-selected="true">Data Barang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="additions-tab" data-toggle="tab" href="#tab-item-additions" role="tab" aria-controls="order" aria-selected="false">Data Barang Lebih</a>
            </li>
        </ul>
  
        <!-- Tab panes -->
        <div class="tab-content pt-2">
          <div class="tab-pane show active" id="tab-item-data" role="tabpanel" aria-labelledby="item-tab">
            <table class="table table-bordered display responsive nowrap" id="opname_item_table" style="width: 100%;">
              <thead>
                <tr>
                  <th>Tgl. Transaksi</th>
                  <th>Name</th>
                  <th>PIC</th>
                  <th>Harga</th>
                  <th>Status</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="tab-pane" id="tab-item-additions" role="tabpanel" aria-labelledby="additions-tab">
            <table id="opname_additions_table" class="table table-bordered display responsive nowrap" style="width: 100%;">
              <thead>
                <tr>
                  <th>No. Asset</th>
                  <th>Name</th>
                  <th>PIC</th>
                  <th>Harga</th>
                  <th>Keterangan Opname</th>
                  <th>Status</th>
                  <th>&nbsp;</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- end of Tab Container -->
      <div class="row btn-group">
        <x-adminlte-button class="btn" type="submit" id="btnSubmit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
        <a href="{{route('stock_opname.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
      </div>
    </input>
  </form>
</div>

{{-- modal approval note --}}
<!-- Modal -->
<div class="modal fade" id="modal-approve" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Keterangan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" id="noteId" name="noteId">
            <x-adminlte-textarea name="stockdtl_note" id="stockdtl_note" fgroup-class="col-md-12" class="form-control"
              maxlength="255" rows="5" placeholder="Isikan Keterangan Asset..."/>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnApproval">Save</button>
        </div>
      </div>
  </div>
</div>
@stop

@section('plugins.Datatables', true)
@section('css')
  <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
@stop

@section('js')
  <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
  <script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>
  <script>

    let tb;
    let url = "{{ route('stock_opname.edit', ':id') }}";
        url = url.replace(':id', '{{ $stock->id }}');
    function setDatatable() {
        tb = $('#opname_item_table').dataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            destroy: true,
            order: [
              [4, 'asc'],
              [0, 'desc'],
            ],
            ajax: url,
            columns: [
            {data: 'stockdtl_obtaindate', name: 'stockdtl_obtaindate', width: '4%',
              render: function (data) {
                if (data) {
                  return moment(data).format('DD/MM/YYYY');
                } else {
                  return '';
                }
              }
            },
            {data: 'stockdtl_name', name: 'stockdtl_name', width: '7%'},
            {data: 'stockdtl_pic', name: 'stockdtl_pic', width: '4%', 
              render: function (data, type, row) {
                if (row.pic != null && data == row.pic.pic_nik) {
                  return row.pic.pic_name;
                } else {
                  return row.site.si_name;
                }
              }
            },
            {data: 'stockdtl_price', name: 'stockdtl_price', width: '4%',
              render: function (data) {
                return AutoNumeric.format(data, {
                  currencySymbol: 'Rp. ',
                  allowDecimalPadding: 'floats'
                });
              }
            },
            {data: 'stockdtl_status', name: 'stockdtl_status', width: '4%', 
              render: function (data, type) {
                if (type === 'display') {
                  if (data == 'FOUND') {
                    return '<span class="badge badge-success">FOUND</span>';
                  } else if (data == 'REMARK') {
                    return '<span class="badge badge-secondary">REMARK</span>';
                  } else {
                    return '<span class="badge badge-danger">OPNAME</span>';
                  }
                }
                return data;
              }
            },
            {
              data: 'id',
              width: '2%',
              render: function (data, type, row) {
                let editDisabled = row.stockdtl_status == 'FOUND' ? 'd-none' : '';                
                let btn = 
                      `<div class="btn-group">
                        <button type="button" class="btn btn-warning ${editDisabled}" data-toggle="modal" onclick="showModalApprove(${row.id});">
                          <i class="fa-solid fa-edit"></i>
                        </button>`;
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

    let tb2;
    function setAdditionsItemTable() {
      let stockId = $('#stockId').val();
      let getAdditionalItems = "{{ route('stock.get-additional-items', ':id') }}";
          getAdditionalItems = getAdditionalItems.replace(':id', stockId);
      tb2 = $('#opname_additions_table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: getAdditionalItems,
            dataSrc: 'data'
        },
        order: [
          [5, 'asc'],
          [0, 'desc'],
        ],
        columns: [
            { data: 'stockdtl_trn_transno', name: 'stockdtl_trn_transno', width: '4%' },
            { data: 'stockdtl_name', name: 'stockdtl_name', width: '7%' },
            { data: 'stockdtl_pic', name: 'stockdtl_pic', width: '4%' },
            { data: 'stockdtl_price', name: 'stockdtl_price', width: '4%',
              render: function (data) {
                return AutoNumeric.format(data, {
                  currencySymbol: 'Rp. ',
                  allowDecimalPadding: 'floats'
                });
              }
            },
            { data: 'stockdtl_note', name: 'stockdtl_note', width: '4%' },
            {data: 'stockdtl_status', name: 'stockdtl_status', width: '4%', 
              render: function (data, type) {
                if (type === 'display') {
                  if (data == 'FOUND') {
                    return '<span class="badge badge-success">FOUND</span>';
                  } else if (data == 'REMARK') {
                    return '<span class="badge badge-secondary">REMARK</span>';
                  } else {
                    return '<span class="badge badge-danger">OPNAME</span>';
                  }
                }
                return data;
              }
            },
            { data: 'id', render: function(data, type, row) {
                return `<button type="button" class="btn btn-warning" data-toggle="modal" onclick="showModalApprove(${data});">
                            <i class="fa-solid fa-edit"></i>
                        </button>`;
            }}
        ]
      });
    }
    $(function(){
        setDatatable();
        setAdditionsItemTable();
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          let target = $(e.target).attr('href');
          if (target == '#tab-item-additions') {
            tb2.ajax.reload();
          }
        });
    });

    $('#stock_scan').on('keypress', function (e) {
      if (e.which == 13) {
        e.preventDefault();
        search();
      }
    });

    function updateItemCounts(stockFound, stockOpname, stockAdditional) {
      stockFound = parseInt(stockFound) || 0;
      stockOpname = parseInt(stockOpname) || 0;
      stockAdditional = parseInt(stockAdditional) || 0;

      $('#stock_found').val(stockFound);
      $('#stock_opname').val(stockOpname);
      $('#stock_additional').val(stockAdditional);

      let stockItemttl = stockFound + stockOpname + stockAdditional;
      $('#stock_itemttl').val(stockItemttl);
    }

    function search() {
      $('#load-overlay-scan').attr('hidden', false);
      let search = $('#stock_scan').val();
      let transno = $('#stock_transno').val();

      $.ajax({
        type: "GET",
        url: "{{ route('stock.checkExistingAsset') }}",
        data: {
          _token: '{{ csrf_token() }}',
          scan: search,
          transno: transno,
        },
        success: function (res) {
          if (res.res == false) {
            $('#load-overlay-scan').attr('hidden', true);
            toastr.error(res.msg, 'Error');
          } else {
            $.ajax({
              type: "POST",
              url: "{{ route('stock.scan') }}",
              data: {
                _token: '{{ csrf_token() }}',
                scan: search,
                transno: transno,
              },
              success: function (res) { 
                if (res.res) {
                  updateItemCounts(res.stock_found, res.stock_opname, res.stock_additional);
                  // $('#stock_opname').val(res.stock_opname);
                  // $('#stock_found').val(res.stock_found);
                  $('#load-overlay-scan').attr('hidden', true);                  
                  tb.fnDraw();
                  toastr.success('Asset berhasil ditemukan.');
                } else {
                  updateItemCounts(res.stock_found, res.stock_opname, res.stock_additional);
                  // $('#stock_additional').val(res.stock_additional);
                  // $('#stock_opname').val(res.stock_opname);
                  $('#load-overlay-scan').attr('hidden', true);
                  $('tr#no-additional-items').remove();
                  let editDisabled = res.data.stockdtl_status == 'FOUND' ? 'd-none' : '';

                  let tbody = $('#opname_additions_table tbody');
                  // append data opname additions in rows
                  let tr = 
                    `<tr>
                      <td>${res.data.stockdtl_trn_transno}</td>
                      <td>${res.data.stockdtl_name ? res.data.stockdtl_name : ''}</td>
                      <td>${res.data.stockdtl_pic ? res.data.stockdtl_pic : ''}</td>
                      <td>${res.data.stockdtl_price ? AutoNumeric.format(res.data.stockdtl_price, {
                        currencySymbol: 'Rp. ',
                        allowDecimalPadding: 'floats',
                      }) : ''}</td>
                      <td>${res.data.stockdtl_desc ? res.data.stockdtl_desc : ''}</td>
                      <td>${res.data.stockdtl_status == 'OPNAME' ? '<span class="badge badge-danger">OPNAME</span>' : '<span class="badge badge-secondary">REMARK</span>'}</td>
                      <td>
                        <button type="button" class="btn btn-warning ${editDisabled}" data-toggle="modal" onclick="showModalApprove(${res.data.id});">
                          <i class="fa-solid fa-edit"></i>
                        </button>
                      </td>
                    </tr>`;
                    $(tr).appendTo(tbody);
                  toastr.success('Asset tambahan berhasil ditemukan.');
                }
              }
            });
          }
        }
      });
    }

    function stockDetailList(row, data) {      
      $('tr#no-additional-items').remove();
      let tbody = $('#opname_additions_table tbody');

      let editDisabled = data.stockdtl_status == 'FOUND' ? 'd-none' : '';
      
      let picDisplay = "";
      if (data.site != null && data.stockdtl_pic == data.site.si_site) {
        picDisplay = data.site.si_name;
      } else if (data.pic != null && data.stockdtl_pic == data.site.si_site) {
        picDisplay = data.pic.pic_name;
      } else {
        picDisplay = '';
      }
      
      let tr = 
        `<tr data-row="${row}" class="${row}" id="${row}">
          <td>
            ${data.stockdtl_trn_transno}
          </td>
          <td>
            ${data.stockdtl_name ? data.stockdtl_name : ''}
          </td>
          <td>
            ${picDisplay}
          </td>
          <td>
            ${data.stockdtl_price ? AutoNumeric.format(data.stockdtl_price , {
              currencySymbol: 'Rp. ',
              allowDecimalPadding: 'floats',
            }) : '' }
          </td>
          <td>
            ${data.stockdtl_note ? data.stockdtl_note : ''}
          </td>
          <td>
            ${data.stockdtl_status == 'OPNAME' ? '<span class="badge badge-danger">OPNAME</span>' : '<span class="badge badge-secondary">REMARK</span>'}
          </td>
          <td>
            <button type="button" class="btn btn-warning ${editDisabled}" data-toggle="modal" onclick="showModalApprove(${data.id});">
              <i class="fa-solid fa-edit"></i>
            </button>
          </td>
        </tr>`;
        $(tr).appendTo(tbody);
    }

    function showModalApprove(id) {
      $('#noteId').val('');
      let showPath = "{{ route('stock.approval-note', ':id') }}";
          showPath = showPath.replace(':id', id);

      $.ajax({
        type: "GET",
        url: showPath,
        success: function (res) {
          $('#noteId').val(res.data.id);
          $('#stockdtl_note').val(res.data.stockdtl_note ? res.data.stockdtl_note : '');
          $('#modal-approve').modal('show');

        }
      });
    }

    $('#btnApproval').on('click', function(e) {
      e.preventDefault();
      let id = $('#noteId').val();
      let note = $('#stockdtl_note').val();
          
      $.ajax({
        type: "POST",
        url: '{{ route('stock.approve') }}',
        data: {
          id: id,
          note: note,
          _token: '{{ csrf_token() }}',
        },
        success: function (res) {
          if (res.res == true) {
            tb.fnDraw();
            tb2.ajax.reload();
            toastr.success('Keterangan berhasil ditambahkan.', 'Success');
            $('#modal-approve .close').click();
          }

          if (res.res == false) {
            toastr.error('Keterangan tidak dapat dibuat.', 'Error');
            $('#modal-approve .close').click();
          }

          if (res.res == 'additional') {
            window.location.reload();
            toastr.success('Keterangan berhasil ditambahkan.', 'Success');
            $('#modal-approve .close').click();
          }
        }
      });
    });
  </script>
@stop

@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Sweetalert2', true)
