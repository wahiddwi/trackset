@extends('adminlte::page')

@section('title', 'Report Asset')

@section('content_header')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">
            Report Asset
            <small>
              <span class="badge badge-primary"></span>
            </small>
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item">Laporan</li>
            <li class="breadcrumb-item active">Asset</li>
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
        <x-adminlte-select2 name="cat_filter" label="Filter Category" fgroup-class="col-md-4" multiple id="cat_filter"
          class="form-control" data-placeholder="All Category">
          @foreach ($category as $cat)
            <option value="{{ $cat->id }}">{{ $cat->cat_code }} - {{$cat->cat_name }}</option>
          @endforeach
        </x-adminlte-select2>

        <x-adminlte-select2 name="brand_filter" label="Filter Brand" fgroup-class="col-md-4" multiple id="brand_filter"
          class="form-control" data-placeholder="All Brand">
          @foreach ($brand as $br)
            <option value="{{ $br->id }}">{{ $br->brand_name }}</option>
          @endforeach
        </x-adminlte-select2>

        <x-adminlte-select2 name="tag_filter" label="Filter Tag" fgroup-class="col-md-4" multiple id="tag_filter"
          class="form-control" data-placeholder="All Tag">
          @foreach ($tag as $tg)
            <option value="{{ $tg->id }}">{{ $tg->tag_name }}</option>
          @endforeach
        </x-adminlte-select2>
      </div>

      <div class="row">
        <x-adminlte-select2 name="type_filter" label="Filter Type" fgroup-class="col-md-4" id="type_filter"
          class="form-control" data-placeholder="All Type">
          <option value="">PIC Type</option>
          <option value="user">User</option>
          <option value="cabang">Cabang</option>
        </x-adminlte-select2>

        <x-adminlte-select2 name="pic_filter" label="Filter PIC" fgroup-class="col-md-4" multiple id="pic_filter"
          class="form-control" data-placeholder="All PIC">
          <option value="" selected disabled>Pilih PIC</option>
        </x-adminlte-select2>

        @php
        $config = [
            "showDropdowns" => true,
            "startDate" => "js:moment().subtract(10, 'days')",
            "endDate" => "js:moment()",
            "minYear" => 2010,
            "maxYear" => "js:parseInt(moment().format('YYYY'),10)",
            "timePicker" => true,
            "timePicker24Hour" => true,
            "cancelButtonClasses" => "btn-danger",
            "locale" => ["format" => "DD MMM YYYY"],
            "opens" => "center",
        ];
        @endphp
        <x-adminlte-date-range name="date_filter" id="date_filter" label="Tanggal" igroup-size="md" :config="$config" error-key="date_filter"
        fgroup-class="col-md-4" value="" placeholder="Pilih rentang tanggal..." enable-old-support>
            <x-slot name="appendSlot">
                <div class="input-group-text bg-dark">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </x-slot>
        </x-adminlte-date-range>
      </div>

      <div class="row">
        <x-adminlte-select2 name="site_filter" label="Filter Cabang" fgroup-class="col-md-4" multiple id="site_filter"
          class="form-control" data-placeholder="All Site">
          @foreach ($sites as $site)
            <option value="{{ $site->si_site }}">{{ $site->si_site }} - {{$site->si_name }}</option>
          @endforeach
        </x-adminlte-select2>

        <x-adminlte-select2 name="loc_filter" label="Filter Lokasi" fgroup-class="col-md-4" multiple id="loc_filter"
          class="form-control" data-placeholder="All location">
        <option value="" selected disabled>Pilih Lokasi</option>

        </x-adminlte-select2>
      </div>

      <div style="display: inline;">
        <x-adminlte-button theme="outline-dark" label="Filter" icon="fas fa-lg fa-magnifying-glass" id="btn_filter" />
        <x-adminlte-button theme="primary" label="Export" icon="fas fa-lg fa-solid fa-file-excel" id="btn_export" />
      </div>
    </div>
  </div>
  
  <div class="card">
    <div class="card-body">
      <table id="asset_table" class="table table-bordered display responsive nowrap" style="width: 100%">
        <thead>
          <tr>
            <th>Kode Asset</th>
            <th>Nama</th>
            <th>Tgl. Perolehan</th>
            <th>Cabang</th>
            <th>Lokasi</th>
            <th>PIC</th>
            <th>Status</th>
            <th></th>
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
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="d-flex justify-content-between w-100">
          <h5 class="modal-title">Report Asset</h5>
          <h6 class="pull-right" id="detail-status"></h6>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <x-adminlte-input name="inv_transno" label="ID" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="inv_site" label="Cabang" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="inv_loc" label="Lokasi" disabled fgroup-class="col-md-4" />
        </div>
        
        <div class="row">
          <x-adminlte-input name="inv_obtaindate" label="Tanggal" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="inv_name" label="Nama" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="inv_pic" label="PIC" disabled fgroup-class="col-md-4" />
        </div>
        
        <div class="row">
          <x-adminlte-input name="inv_price" label="Harga Beli" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="inv_current_price" label="Harga Terakhir" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="inv_accumulate_dep" label="Akumulasi Depresiasi" disabled fgroup-class="col-md-4" />
        </div>

        <div class="row">
          <x-adminlte-input name="inv_tag" label="Tag" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="inv_merk" label="Brand" disabled fgroup-class="col-md-4" />
        </div>
        
        <div class="row">
          <x-adminlte-textarea name="inv_desc" fgroup-class="col-md-12" class="form-control" label="Keterangan Pemakaian"
            maxlength="255" disabled />
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

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
@stop

@section('js')
  <script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>
  <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
  <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
  <script>
    let tb;
    $('#asset_table').dataTable();
    function filterDatatable() {
      tb = $('#asset_table').DataTable({
        scroll:  true,
        scrollX: true,
        processing: true,
        serverSide: true,
        destroy: true,
        initComplete: function () {
          let api = this.api();
          let searchWait = 0;
          let searchWaitInterval;
          $('.dataTables_filter input')
            .unbind()
            .bind('input', function (e) {
              let item = $(this);
              searchWait = 0;
              if (!searchWaitInterval) searchWaitInterval = setInterval(() => {
                searchTerm = $(this).val();
                clearInterval(searchWaitInterval);
                searchWaitInterval = '';
                api.search(searchTerm).draw();
                searchWait = 0;
                searchWait++;
              }, 1000);
              return;
            });
        },
        order: [
          [6, 'asc'],
          [2, 'desc'],
          [0, 'asc'],
        ],
        ajax: {
          url: "{{ route('report.filter') }}",
          data: {
            category: $('#cat_filter').val(),
            brand: $('#brand_filter').val(),
            tag: $('#tag_filter').val(),
            type: $('#type_filter').val(),
            pic: $('#pic_filter').val(),
            site: $('#site_filter').val(),
            loc: $('#loc_filter').val(),
            date: $('#date_filter').val(),
          }
        },
        columns: [{
          data: 'inv_transno',
          name: 'inv_transno'
        },
        {
          data: 'inv_name',
          name: 'inv_name'
        },
        {
          data: 'inv_obtaindate',
          name: 'inv_obtaindate',
          render: function (data) {
              return moment(data).format('DD MMM yyyy');
          }
        },
        {
          data: 'asset_site',
          name: 'asset_site'
        },
        {
          data: 'asset_loc',
          name: 'asset_loc'
        },
        {
          data: 'asset_pic',
          name: 'asset_pic'
        },
        {
          data: 'inv_status',
          name: 'inv_status',
          className: 'text-center',
          render: function (data, type) {
            if (type === 'display') {
              let state = 'danger';
              switch (data) {
                case 'DRAFT':
                  state = 'primary';
                  break;
                case 'ONHAND':
                  state = 'success';
                  break;
                case 'RSV':
                  state = 'secondary';
                  break;
                case 'SELL':
                  state = 'danger';
                  break;
              }
              return `<span class="badge badge-${state}">${data}</span>`;
            }
            return data;
          }
        },
        {
          data: 'id',
          orderable: false,
          filterable: false,
          searchable: false,
          width: '1%',
          render: function (data, type, row) {
            let btn =
              '<div class="btn-group" role="group"><button class="btn btn-primary" onclick="return showData(\'' + data + '\');" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></button> ';
              
            @can(session('menuId') . '_update')
                let editPath = "{{ route('report.edit', ':id') }}";
                    editPath = editPath.replace(':id', data);
                
                let update_disabled = (row.inv_status == "ONHAND" || row.inv_status == "DRAFT") ? "" : "d-none";
                btn += '<a href="' + editPath + '" class="btn btn-warning ' + update_disabled + '" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a> '
            @endcan

            @can(session('menuId') . '_print')
              let qrPath = "{{ route('asset.qr', ':id') }}";
                      qrPath = qrPath.replace(':id', data);
              let qr_disabled = row.inv_status != "ONHAND" ? "d-none" : "";
              btn += '<a href="' + qrPath + '" class="btn btn-primary ' + qr_disabled + '" target="_blank" data-toggle="tooltip" title="QR"><i class="fa-solid fa-qrcode"></i></a>';

              let barcodePath = "{{ route('asset.barcode', ':id') }}";
                      barcodePath = barcodePath.replace(':id', data);
              let barcode_disabled = row.inv_status != "ONHAND" ? "d-none" : "";
              btn += '<a href="' + barcodePath + '" class="btn ' + barcode_disabled + '" style="background-color: #d3d3d3; color: black;" target="_blank" data-toggle="tooltip" title="Barcode"><i class="fa-solid fa-barcode"></i></a>';
            @endcan

            @can(session('menuId') . '_delete')
                let remove_disabled = (row.inv_status == "CANCEL" || row.inv_status == "SELL") ? "d-none" : "";

                btn += '<button class="btn btn-danger '+ remove_disabled +'" onclick="return deleteRecord(\'' + data + '\');" data-toggle="tooltip" title="DELETE"><i class="fa fa-trash"></i></button>';
            @endcan

            btn += '</div>';

            return btn;
          }
        },
      ]
      });
    }

    $('#type_filter').on('change', function () {
      let typeValue = $(this).val();
      $.ajax({
        type: "GET",
        url: "{{ route('report.get-pic') }}",
        data: {
          type: typeValue,
        },
        dataType: "json",
        success: function (res) {
          $('#pic_filter').empty();
          $.each(res.data, function (i, pic) {
            $('#pic_filter').append($('<option>', {
              value: pic.pic_nik,
              text: typeValue == 'user' ? pic.pic_nik + " - " + pic.pic_name : pic.si_site + " - " + pic.si_name
            }));
          });
        }
      });
    });

    $('#pic_filter').on('select2:open', function (e) {
      let typeValue = $('#type_filter').val();
      if (!typeValue) {
        e.preventDefault();
        toastr.error('Silahkan pilih "Type" terlebih dahulu sebelum memilih "PIC".', 'Error');
        $('#pic_filter').select2('close');
        return;
      }
    });

    $('#loc_filter').on('select2:open', function (e) {
      let siteValue = $('#site_filter').val();
      
      if (Array.isArray(siteValue) && siteValue.length == 0) {
        e.preventDefault();
        toastr.error('Silahkan pilih "Cabang" terlebih dahulu sebelum memilih "Lokasi".', 'Error');
        $('#loc_filter').select2('close');
        return;
      }
    });

    $('#site_filter').on('change', function () {
      let siteId = $(this).val();
      $.ajax({
        type: "GET",
        url: "{{ route('report.get-loc') }}",
        data: {
          loc_site: siteId,
        },
        dataType: "json",
        success: function (res) {
          $('#loc_filter').html('<option value="" selected disabled>Pilih Lokasi</option>');
          $.each(res, function (i, val) { 
            $('#loc_filter').append(`<option value="${val.id}">${val.loc_id} - ${val.loc_name}</option>`);
          });
        }
      });      
    });

    $(function () {
      $('#btn_filter').on('click', function () {
        filterDatatable();
      });

      $('#btn_export').on('click', function () {
        exportData();
      });
    });

    function showData(id) {
      $('#load-overlay').show();
      $('#modal-detail').modal('show');
      $('#modal-detail input').val(null);
      $('#modal-detail textarea').html(null);
      $('#modal-detail span').html(null);

      let showPath = "{{ route('report.show', ':id') }}";
          showPath = showPath.replace(':id', id);

      $.ajax({
        type: "GET",
        url: showPath,
        success: function (res) {
          if (res.res) {
            $('#load-overlay').hide();
            $('#inv_transno').val(res.report.inv_transno);
            $('#inv_site').val(res.report.site.si_name);
            $('#inv_loc').val(res.report.location.loc_name);
            $('#inv_obtaindate').val(moment(res.report.inv_obtaindate).format('DD MMM yyyy'));
            $('#inv_name').val(res.report.inv_name);
            $('#inv_pic').val(res.report.pic != null ? res.report.pic.pic_name : res.report.site.si_name);
            $('#inv_price').val(AutoNumeric.format(res.report.inv_price, {currencySymbol: 'Rp. ', allowDecimalPadding: 'floats'}));
            $('#inv_current_price').val(AutoNumeric.format(res.report.inv_current_price, {currencySymbol: 'Rp. ', allowDecimalPadding: 'floats'}));
            $('#inv_accumulate_dep').val(AutoNumeric.format(res.report.inv_accumulate_dep, {currencySymbol: 'Rp. ', allowDecimalPadding: 'floats'}));
            $('#inv_tag').val(res.report.tag.tag_name);
            $('#inv_merk').val(res.report.merk.brand_name);
            $('#inv_desc').val(res.report.inv_desc);
          }
          
        }
      });
    }

    function deleteRecord(id) {
      Swal.fire({
        title: 'Are you sure remove this?',
        text: "You won't be able to modify once it's removed.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes!'
      }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  type: "POST",
                  url: `{{ url('report') }}/${id}/delete`,
                  data: {
                    id: id,
                    _token: "{{ csrf_token() }}",
                  },
                  dataType: "json",
                  success: function (res) {
                      if (res.res) {
                          toastr.success('Asset berhasil dihapus', 'Success');
                          // tb.fnDraw();
                          tb.ajax.reload(null, false);
                      } else {
                          toastr.error('mohon coba sesaat lagi.', 'Error');
                      }
                  }
              });
          }
      });
    }

    function exportData() {            
      let category = $('#cat_filter').val();
      let brand = $('#brand_filter').val();
      let tag = $('#tag_filter').val();
      let type = $('#type_filter').val();
      let pic = $('#pic_filter').val();
      let site = $('#site_filter').val();
      let loc = $('#loc_filter').val();
      let date = $('#date_filter').val();

      let data =  {
          category: category,
          brand: brand,
          tag: tag,
          type: type,
          pic: pic,
          site: site,
          loc: loc,
          date: date
        };

      $.ajax({
        xhrFields: {
          responseType: 'blob',
        },
        type: "GET",
        data: data,
        url: "{{ route('report.export') }}",
        success: function(result, status, xhr) {
            var disposition = xhr.getResponseHeader('content-disposition');
            var matches = /"([^"]*)"/.exec(disposition);
            var filename = (matches != null && matches[1] ? matches[1] : 'report.xlsx');

            // The actual download
            var blob = new Blob([result], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename;

            document.body.appendChild(link);

            link.click();
            document.body.removeChild(link);
        },
        error: function (res, status, xhr) {
          console.log(xhr, res, status);
          
        }
      });
    }
  </script>
@stop

@section('plugins.DateRangePicker', true)
@section('plugins.Select2', true)
@section('plugins.Datatables', true)
