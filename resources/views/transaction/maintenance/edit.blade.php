@extends('adminlte::page')

@section('title', 'Maintenance - Edit')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Edit <small>Maintenance</small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('maintenance.index') }}">Maintenance</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<form action="{{ route('maintenance.update', $maintenance->id) }}" method="POST" id="editForm">
  @csrf
  @method('PATCH')
  <div class="card">
      <div class="card-body">
        <input type="hidden" name="main_company" id="main_company" value="{{ $maintenance->main_company }}">
        <input type="hidden" name="line_count" id="line_count" value="{{ $maintenance->asset_count }}">
        <input type="hidden" name="total_pay" id="total_pay" value="{{ $maintenance->main_total_cost }}">

        <div class="row">
            <x-adminlte-input name="main_transno" label="Maintenance" placeholder="AUTONUMBER" maxlength="10"
                fgroup-class="col-md-3" error-key="main_transno" disabled label-class="must-fill" value="{{ $maintenance->main_transno }}" />

            <x-adminlte-select2 name="main_vendor" label="Pilih vendor" fgroup-class="col-md-3" label-class="must-fill" error-key="maindtl_vendor" class="form-control">
              <option value="" selected disabled>Pilih Vendor</option>
              @foreach ($vendor as $vdr)
                  <option value="{{$vdr->id}}" {{ $maintenance->main_vendor == $vdr->id ? 'selected' : ''}}>{{ $vdr->vdr_name }}</option>
              @endforeach
            </x-adminlte-select2>
            
              @php
                $config = [
                    'format' => 'DD MMM YYYY',
                    'dayViewHeaderFormat' => 'MMM YYYY',
                ];
              @endphp
              <x-adminlte-input-date name="main_transdate" id="main_transdate" label="Tgl. Transaksi" igroup-size="md" error-key="main_transdate"
                fgroup-class="col-md-3 offset-md-3" :config="$config" value="{{ date('d M Y') }}" label-class="must-fill" enable-old-support>
                <x-slot name="appendSlot">
                  <div class="input-group-text bg-dark">
                    <i class="fas fa-calendar-day"></i>
                  </div>
                </x-slot>
              </x-adminlte-input-date>
        </div>

        <div class="row">
          <x-adminlte-select2 name="maintenance_asset" id="search" label="Pilih No. Asset" fgroup-class="col-md-6" error-key="maintenance_asset_transno" class="form-control">
            <x-slot name="appendSlot">
              <div class="input-group-text text-primary bg-dark">
                <a href="javascript:" type="search" id="btn-search"><i class="fa-brands fa-searchengine"></i> Cari!</a>
              </div>
            </x-slot>
            <option value="" selected disabled>Pilih No. Asset</option>
            @foreach ($asset as $item)
                <option value="{{$item->id}}" data-transno="{{ $item->inv_transno }}" data-name="{{ $item->inv_name }}">{{ $item->inv_transno.' - '.$item->inv_name }}</option>
            @endforeach
          </x-adminlte-select2>
        </div>
      </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="card-title">
          <small>
            Asset List
          </small>
        </div>
        <div class="table-responsive w-100">
          <table class="table table-custom table-striped table-sm" id="po_detail">
              <thead>
                  <tr>
                      <th class="text-center">No</th>
                      <th class="text-center" style="width: 10%;">No. Asset</th>
                      <th class="text-center">Asset</th>
                      <th class="text-center">Tipe Maintenance</th>
                      <th class="text-center" style="width: 5%;">Tgl. Terakhir Maintenance</th>
                      <th class="text-center must-fill">Keterangan</th>
                      <th class="text-center must-fill" style="width: 5%;">Kilometer</th>
                      <th class="text-center must-fill" style="width: 15%">Nominal</th>
                      <th class="text-center" style="width: 10px"></th>
                  </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
                  <tr>
                      <th colspan="7" class="text-right">Total</th>
                      <th class="text-right"><span id="totalPayment">Rp. 0</span></th>
                      <th></th>
                  </tr>
              </tfoot>
          </table>
        </div>
      </div>

    <div class="row btn-group">
        <x-adminlte-button class="btn" label="Update" theme="success" icon="fas fa-lg fa-save"
            id="update_btn" />
        <a href="{{ route('maintenance.index') }}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i>
            Cancel</a>
    </div>
    </div>
    <div class="overlay" id="load-overlay" hidden>
      <i class="fas fa-2x fa-sync-alt fa-spin"></i>
    </div>
  </div>
</form>
@stop

@section('css')
    <style>
        table td .form-group {
            margin-bottom: 0px !important;
        }

        /* Container untuk horizontal scroll */
        .table-scroll-x {
          overflow-x: auto;
          -webkit-overflow-scrolling: touch;
          border: 1px solid #dee2e6;
          border-radius: 0.5rem;
        }

        /* Styling tabel */
        .table-custom {
          min-width: 1200px;
          border-collapse: collapse;
          width: 100%;
        }

        .table-custom thead {
          background-color: #f8f9fa;
          color: #343a40;
        }

        .table-custom th,
        .table-custom td {
          padding: 0.75rem;
          text-align: center;
          vertical-align: middle;
          border: 1px solid #dee2e6;
          font-size: 0.875rem;
        }

        .table-custom tbody tr:hover {
          background-color: #f1f3f5;
        }

        .table-custom tfoot {
          font-weight: bold;
          background-color: #f8f9fa;
        }

        /* Responsive tweaks */
        @media (max-width: 768px) {
          .table-custom th,
          .table-custom td {
            font-size: 0.75rem;
            padding: 0.5rem;
          }
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>
    <script>
        $(document).ready(function () {
          // assetNo = [];
          @foreach($maintenance->detail as $dtl)
            // assetNo.push('{{ $dtl->maindtl_asset_transno }}');
            editRow({{ $dtl->maindtl_line }}, @php echo json_encode($dtl->toArray()); @endphp)
          @endforeach          

          $('#po_detail').on('click', 'button.del_btn', function() {
            let row = $(this).closest('tr');
            let assetId = $(this).closest('tr').find('input[name="asset_transno[]"]').val();
            // assetNo.splice($.inArray(assetId, assetNo), 1);
            $(row).remove();

              let tbody = $('#po_detail tbody tr');
              tbody.each(function(ind, el) {
                  $(el).attr('data-row', ind + 1);
                  $($(el).find('span.index')[0]).html(ind + 1);
                  $($(el).find('input.hidden_index')[0]).val(ind + 1);
              });
              calcTotals();

              $('#line_count').val(tbody.length);
          });

          $('#main_tech_telp').on('input', function (e) {
            $(this).val($(this).val().replace(/[^0-9+]/g, ''));
          });

          $('input, select, textarea').on('change', function() {
            $(this).removeClass('is-invalid');
          });

          $('#po_detail').on('change', 'input, select, textarea', function() {
            $(this).removeClass('is-invalid');
          });

          $('#search').on('change', function (e) {
            e.preventDefault();
            let search = $(this).find(':selected').val();
          });

          $('#btn-search').on('click', function (e) {
            e.preventDefault();
            $('#load-overlay').removeAttr('hidden');

            let search = $('#search').val();
            let transno = $('#search').find(':selected').data('transno').trim();
            let company = $('#main_company').val();
            
            // if (assetNo.includes(transno)) {
            //   $('#load-overlay').attr('hidden', true);
            //   return toastr.error('No. Asset sudah digunakan', 'Error');
            // }

            $.ajax({
              type: "GET",
              url: "{{ route('maintenance.search') }}",
              data: {
                search: search,
                company: company,
                transno: transno,
              },
              dataType: "json",
              success: function (res) {
                // assetNo.push(transno);
                $('#load-overlay').attr('hidden', true);
                if (res.res) {
                  // if (res.result.inv_company != company) {
                  //   $('#load-overlay').attr('hidden', true);
                  //   return toastr.error('Penjualan Harus dilakukan pada PT yang sama', 'Error');
                  // }

                  let row = $('#po_detail tbody tr').length + 1;                  
                  addRow(row, res.result);
                } else {
                  $('load-overlay').attr('hidden', true);
                  return toastr.error('No. Asset tidak ditemukan.', 'Error');
                }
              }
            });
          });

          $('#update_btn').on('click', function () {
            // validation
            if (!$('#main_vendor').val()) {
              $('#main_vendor').addClass('is-invalid');
              return toastr.error('Mohon pilih Vendor terlebih dahulu', 'Error');
            }

            let tbody = $('#po_detail tbody tr');
            let flag = null;
            tbody.each(function(ind, el) {
              if ($($(el).find('input.hidden-price')[0]).val() <= 0) flag = [+ind + 1, 'price'];
              if ($($(el).find('input.remark')[0]).val().trim() == '') flag = [+ind + 1, 'desc'];
              if ($($(el).find('input.mileage')[0]).val().trim() == '') flag = [+ind + 1, 'mileage'];
            });

            if (flag) {
              if (flag[1] == 'desc') {
                $('tr[data-row="' + flag[0] + '"] .remark').addClass('is-invalid');
                return toastr.error('Keterangan tidak boleh kosong!', 'Error');
              } else if (flag[1] == 'mileage') {
                $('tr[data-row="' + flag[0] + '"] .mileage').addClass('is-invalid');
                return toastr.error('Kilometer tidak boleh kosong!', 'Error');
              } else {
                $('tr[data-row="' + flag[0] + '"] .price').addClass('is-invalid');
                return toastr.error('Nominal tidak boleh kosong!', 'Error');
              }
            }

            if ($('#total_pay').val() <= 0) {
              return toastr.error('Total Biaya tidak boleh 0', 'Error');
            }

            $('#editForm').submit();
          });

          $('#totalPayment').html(AutoNumeric.format({{ $maintenance->main_total_cost }}, {
            currencySymbol: 'Rp. ',
            allowDecimalPadding: 'floats'
          }));
        });

        function editRow(row, data) {
          let tbody = $('#po_detail tbody');
          let btn = 
            '<div class="btn-group" role="group"><button class="btn btn-danger del_btn" type="button"><i class="fa fa-times-circle"></i></button></div>';
            
          let tr = `
            <tr data-row="${row}">
              <td class="text-center align-middle"><span class="index">${row}</span>
                <input type="hidden" class="hidden_index" name="line[]" value="${row}">
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input value="${data.maindtl_asset_transno}" class="form-control" id="asset_transno" maxLength="255" disabled>
                    <input type="hidden" name="asset_transno[]" value="${data.maindtl_asset_transno}">
                    <input type="hidden" name="asset_id[]" value="${data.maindtl_asset_id}">
                    <input type="hidden" name="asset_site[]" value="${data.maindtl_site}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input value="${data.maindtl_asset_name}" class="form-control" maxLength="255" disabled>
                    <input type="hidden" name="asset_name[]" value="${data.maindtl_asset_name}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <select name="cat_mtn[]" class="form-control">
                      <option disabled selected>Pilih Item</option>
                      @foreach($cat_maintenance as $cat_mtn)
                        <option value="{{ $cat_mtn->id }}" data-type="{{ $cat_mtn->mtn_type }}" ${data.maindtl_cat_mtn == {{$cat_mtn->id}} ? 'selected' : ''}>{{ $cat_mtn->mtn_type }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input type="text" name="mtn_lastdate[]" value="${data.maindtl_lastdate ? data.maindtl_lastdate : 'Belum pernah Maintenance'}" class="mtn_lastdate" readonly disabled>
                    <input type="hidden" name="hidden_lastdate[]" class="hidden_lastdate" value="${data.maindtl_lastdate}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input name="remark[]" value="${data.maindtl_desc}" class="form-control remark" maxLength="255">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control mileage" maxLength="255" value="${data.maindtl_mileage}" name="mileage[]">
                    <input class="hidden-mileage" type="hidden" name="hidden_mileage[]" value="${data.maindtl_mileage}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control price" maxLength="255" value="${data.maindtl_cost}">
                    <input class="hidden-price" type="hidden" name="price[]" value="${data.maindtl_cost}">
                  </div>
                </div>
              </td>
              <td>${btn}</td>
            </tr>
          `;  
          $(tr).appendTo(tbody);

          $('tr[data-row="' + row + '"] select').select2({
            "theme": "bootstrap4"
          });

          let rowSelector = `tr[data-row="${row}"]`;

          $(`${rowSelector} select[name="cat_mtn[]"]`).on('change', function () {
            let catId = $(this).val();
            let assetId = $(`${rowSelector} input[name="asset_id[]"]`).val();

            $.ajax({
              type: "GET",
              url: "{{ route('maintenance.lastdate') }}",
              data: {
                asset_id: assetId,
                cat_mtn_id: catId,
              },
              success: function (res) {
                $(`${rowSelector} .mtn_lastdate`).val(res.date ?? 'Belum pernah Maintenance');
                $(`${rowSelector} .hidden_lastdate`).val(res.date ?? '');
              }
            });
          });
          
          let [price] = AutoNumeric.multiple('tr[data-row="' + row + '"] .price', {
            currencySymbol: 'Rp. ',
            allowDecimalPadding: 'floats',
            modifyValueOnWheel: false
          });

          let [mileage] = AutoNumeric.multiple('tr[data-row="' + row + '"] .mileage', {
            currencySymbol: ' KM',
            allowDecimalPadding: 'floats',
            modifyValueOnWheel: false,
            currencySymbolPlacement: 's'
          });
        }

        function addRow(row, data) {
          let tbody = $('#po_detail tbody');
          let btn = 
            '<div class="btn-group" role="group"><button class="btn btn-danger del_btn" type="button"><i class="fa fa-times-circle"></i></button></div>';

          let tr = `
            <tr data-row="${row}">
              <td class="text-center align-middle"><span class="index">${row}</span>
                <input type="hidden" class="hidden_index" name="line[]" value="${row}">
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input value="${data.inv_transno}" class="form-control" id="asset_transno" maxLength="255" disabled>
                    <input type="hidden" name="asset_transno[]" value="${data.inv_transno}">
                    <input type="hidden" name="asset_id[]" value="${data.id}">
                    <input type="hidden" name="asset_site[]" value="${data.inv_site}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input value="${data.inv_name}" class="form-control" maxLength="255" disabled>
                    <input type="hidden" name="asset_name[]" value="${data.inv_name}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <select name="cat_mtn[]" class="form-control">
                      <option disabled selected>Pilih Item</option>
                      @foreach($cat_maintenance as $cat_mtn)
                        <option value="{{ $cat_mtn->id }}" data-type="{{ $cat_mtn->mtn_type }}">{{ $cat_mtn->mtn_type }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input type="text" name="mtn_lastdate[]" class="mtn_lastdate" readonly disabled>
                    <input type="hidden" name="hidden_lastdate[]" class="hidden_lastdate">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input name="remark[]" value="" class="form-control remark" maxLength="255">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control mileage" maxLength="255" value="" name="mileage[]">
                    <input class="hidden-mileage" type="hidden" name="hidden_mileage[]" value="0">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control price" maxLength="255" value="">
                    <input class="hidden-price" type="hidden" name="price[]" value="">
                  </div>
                </div>
              </td>
              <td>${btn}</td>
            </tr>
          `;
          $(tr).appendTo(tbody);

          $('tr[data-row="'+ row +'"] select').select2({
            "theme": "bootstrap4"
          });

          $('tr[data-row="' + row + '"] select[name="cat_mtn[]"]').on('change', function () {
            const $row = $(this).closest('tr');
            const cat_mtn_id = $(this).val();
            const asset_id = $row.find('input[name="asset_id[]"]').val();

            if (!asset_id || !cat_mtn_id) return;
            
            $.ajax({
              type: "GET",
              url: "{{ route('maintenance.lastdate') }}",
              data: {
                asset_id: asset_id,
                cat_mtn_id:cat_mtn_id,
              },
              success: function (res) {
                const lastdate = res.date || 'Belum pernah Maintenance';
                const hiddenLastdate = res.date || '';
                $row.find('.mtn_lastdate').val(lastdate);
                $row.find('.hidden_lastdate').val(hiddenLastdate);

              }
            });
          });

          let [price] = AutoNumeric.multiple('tr[data-row="' + row + '"] .price', {
            currencySymbol: 'Rp. ',
            allowDecimalPadding: 'floats',
            modifyValueOnWheel: false
          });

          let [mileage] = AutoNumeric.multiple('tr[data-row="' + row + '"] .mileage', {
            currencySymbol: ' KM',
            allowDecimalPadding: 'floats',
            modifyValueOnWheel: false,
            currencySymbolPlacement: 's'
          });

          $('tr[data-row="' + row + '"] .mileage').on('change', function () {
            $('tr[data-row="'+ row +'"] .hidden-mileage').val(mileage.getNumber());
          })

          $('tr[data-row="' + row + '"] .price').on('change', function() {
                $('tr[data-row="' + row + '"] .hidden-price').val(price.getNumber());

                calcTotals();
            });

            $('#line_count').val(row);
        }

        function calcTotals() {
            let tbody = $('#po_detail tbody tr');
            let price = 0;
            tbody.each(function(_, el) {
                price += +$($(el).find('input.hidden-price')[0]).val();
            });

            $('#total_pay').val(price);
            $('#totalPayment').html(AutoNumeric.format(price, {
                currencySymbol: 'Rp. ',
                allowDecimalPadding: 'floats'
            }));
        }
    </script>
@stop

@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.KrajeeFileinput', true)
