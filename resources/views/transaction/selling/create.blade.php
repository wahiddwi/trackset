@extends('adminlte::page')

@section('title', 'Selling - Create')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                  Selling
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Selling</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('selling.store') }}" method="POST">
                @csrf
                <input type="hidden" name="cust_no" id="cust_no">
                <input type="hidden" name="cust_name" id="cust_name">
                <input type="hidden" name="cust_addr" id="cust_addr">
                <input type="hidden" name="cust_telp" id="cust_telp">
                <input type="hidden" name="cust_wa" id="cust_wa">
                <input type="hidden" name="cust_email" id="cust_email">
                <input type="hidden" name="sell_company" id="sell_company" value="{{ $selected_company }}">
                <input type="hidden" name="sell_site" id="sell_site">

                <div class="row">
                  <x-adminlte-input name="sell_no" label="No. Transaksi" placeholder="AUTO GENERATED" igroup-size="md" fgroup-class="col-md-6" error-key="sell_no" disabled/>
                  @php
                    $config = [
                        'format' => 'DD MMM YYYY',
                        'dayViewHeaderFormat' => 'MMM YYYY',
                    ];
                  @endphp
                  <x-adminlte-input-date name="sell_transdate" label="Tgl. Transaksi" igroup-size="md" error-key="sell_transdate"
                    fgroup-class="col-md-6" :config="$config" value="{{ date('d M Y') }}" enable-old-support>
                    <x-slot name="appendSlot">
                      <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-day"></i>
                      </div>
                    </x-slot>
                  </x-adminlte-input-date>
                </div>
                <div class="row">
                  <x-adminlte-select name="sell_cust" id="sell_cust" label="Customer" label-class="must-fill" fgroup-class="col-md-12" error-key="sell_cust" class="form-control">
                    <option value="" selected disabled>Pilih Customer</option>
                    @foreach ($customer as $cust)
                        <option value="{{ $cust->id }}"
                                data-name="{{ $cust->cust_name }}"
                                data-no="{{ $cust->cust_no }}"
                                data-addr="{{ $cust->cust_addr }}"
                                data-telp="{{ $cust->cust_telp }}"
                                data-wa="{{ $cust->cust_wa}}"
                                data-email="{{ $cust->cust_email }}"
                        >{{ $cust->cust_name }}</option>
                    @endforeach
                </x-adminlte-select>
                </div>
                <div class="row">
                  <x-adminlte-input name="sell_cust_name" label="Name" id="sell_cust_name" igroup-size="md" fgroup-class="col-md-6" error-key="sell_cust_name" disabled/>
                  <x-adminlte-input name="sell_cust_no" label="No. Identitas" id="sell_cust_no" igroup-size="md" fgroup-class="col-md-6" error-key="sell_cust_no" disabled/>
                </div>
                <div class="row">
                  <x-adminlte-textarea name="sell_cust_addr" id="sell_cust_addr" label="Alamat" igroup-size="md" fgroup-class="col-md-12" error-key="sell_cust_addr" rows=4 disabled/>
                </div>
                <div class="row">
                  <x-adminlte-input name="sell_cust_telp" label="Tel" id="sell_cust_telp" igroup-size="md" fgroup-class="col-md-4" error-key="sell_cust_telp" disabled/>
                  <x-adminlte-input name="sell_cust_wa" label="Whatsapp" id="sell_cust_wa" igroup-size="md" fgroup-class="col-md-4" error-key="sell_cust_wa" disabled/>
                  <x-adminlte-input name="sell_cust_email" label="Email" id="sell_cust_email" igroup-size="md" fgroup-class="col-md-4" error-key="sell_cust_email" disabled/>
                </div>
                <div class="row">
                  <x-adminlte-textarea name="sell_desc" label="Keterangan" label-class="must-fill" igroup-size="md" fgroup-class="col-md-12" error-key="sell_desc" rows=4/>
                </div>
                <hr>
                <div class="row">
                  <x-adminlte-select2 name="sell_asset_no" id="search" label="Pilih No. Asset" fgroup-class="col-md-6" error-key="sell_asset_no" class="form-control">
                    <x-slot name="appendSlot">
                      <div class="input-group-text text-primary bg-dark">
                        <a href="javascript:" type="search" id="btn-search"><i class="fa-brands fa-searchengine"></i> Cari!</a>
                      </div>
                    </x-slot>
                    <option value="" selected disabled>Pilih No. Asset</option>
                    @foreach ($asset as $item)
                        <option value="{{$item->inv_transno}}">{{ $item->inv_transno.' - '.$item->inv_name }}</option>
                    @endforeach
                </x-adminlte-select2>
                </div>
    
                <table id="selling_table" class="table table-bordered display responsive nowrap" style="width: 100%">
                  <thead>
                      <tr>
                          <th>No. Asset</th>
                          <th>Barang</th>
                          <th>Keterangan</th>
                          <th>Harga Sekarang</th>
                          <th>Harga Jual <span class="text-danger">*</span></th>
                          <th></th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
    
                <div class="row btn-group">
                    <x-adminlte-button class="btn" type="submit" id="btn_submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                    <a href="{{route('selling.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
        <div class="overlay" id="load-overlay" hidden>
          <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>
</div>

@stop
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Select2', true)
@section('plugins.KrajeeFileinput', true)


@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
    <style>
        .btn-group {
            margin-left: 2px;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>

    <script>
        $(document).ready( function () {
          assetNo = [];

            $(document).on('click', '#btnRemove', function () {
                let id = $(this).data('id');
                let transno = $('#inv_transno').val();

                assetNo.splice($.inArray(transno, assetNo), 1);

                 $(this).closest('#'+ id +'').remove();
                
            });

          $('#sell_cust').on('change', function () {
            let selected = $(this).find(':selected');
            let custNo = selected.data('no');
            let custName = selected.data('name');
            let custAddr = selected.data('addr');
            let custTelp = selected.data('telp');
            let custWa = selected.data('wa');
            let custEmail = selected.data('email');

            $('#sell_cust_name').val(custName);
            $('#sell_cust_no').val(custNo);
            $('#sell_cust_addr').val(custAddr);
            $('#sell_cust_telp').val(custTelp);
            $('#sell_cust_wa').val(custWa);
            $('#sell_cust_email').val(custEmail);

            $('#cust_name').val(custName);
            $('#cust_no').val(custNo);
            $('#cust_addr').val(custAddr);
            $('#cust_telp').val(custTelp);
            $('#cust_wa').val(custWa);
            $('#cust_email').val(custEmail);
                     
          });

          $('#search').on('change', function (e) {
            e.preventDefault();
            let search = $(this).find(':selected').val();
          })

          $('#btn-search').on('click', function (e) {
            e.preventDefault();
            $('#load-overlay').removeAttr('hidden');

            let search = $('#search').val();
            let company = $('#sell_company').val();
            

            if (assetNo.includes(search)) {
              $('#load-overlay').attr('hidden', true);
              return toastr.error('No. Asset sudah digunakan', 'Error');
            }

            $.ajax({
              type: "GET",
              url: "{{ route('selling.search') }}",
              data: {
                search: search,
                company: company,
              },
              dataType: "json",
              success: function (res) {
                assetNo.push(search);
                toggleOverlay(false);
                $('#sell_company').val(res.result.site.si_company);
                $('#sell_site').val(res.result.site.si_site);
                
                if (res.res == true) {
                  if (res.result.inv_company != company) {
                    toggleOverlay(false);
                    return toastr.error('Penjualan Harus dilakukan pada PT yang sama', 'Error');
                  }

                  let tbody = $('#selling_table tbody');

                  let tr = 
                    `<tr class="${res.result.id}" id="${res.result.id}">
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control inv_transno" id="inv_transno" value="${res.result.inv_transno}" disabled/>
                            <input type="hidden" name="dtl_asset_transno[]" value="${res.result.inv_transno}">
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control name" value="${res.result.inv_name}" disabled/>
                            <input value="${res.result.inv_name}" type="hidden" name="dtl_asset_name[]"/>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control remark" value="" name="dtl_sell_desc[]"/>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control price" value="${res.result.inv_current_price}" disabled/>
                            <input type="hidden" name="dtl_asset_inv_price[]" value="${res.result.inv_current_price}">
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control hidden_sell_price" id="hidden_sell_price" name="hidden_sell_price[]" type="hidden" />
                            <input class="form-control sell_price" value="" name="dtl_asset_sell_price[]" id="dtl_asset_sell_price" />
                          </div>
                        </div>
                      </td>
                      <td>
                        <a href="javascript:" type="button" id="btnRemove" data-id="${res.result.id}" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
                      </td>
                    </tr>`;

                  $(tr).appendTo(tbody);

                  initializeAutoNumeric();

                } else {
                  toggleOverlay(false);
                  return toastr.error('No. Asset tidak ditemukan.', 'Error');
                }
              }
            });
          });
          
          // initialize AutoNumeric to all input with class price
          function initializeAutoNumeric() {
            AutoNumeric.multiple('.price', {
              currencySymbol: 'Rp. ',
              allowDecimalPadding: 'floats',
            });

            //initialize sell price
            $('.sell_price').each(function () {
              if (!$(this).data('autoNumeric')) {
                const sellPrice = new AutoNumeric(this, 0, {
                  currencySymbol: 'Rp. ',
                  allowDecimalPadding: 'floats',
                  modifyValueOnWheel: false
                });

                $(this).data('autoNumeric', sellPrice);

                $(this).on('change', function () {
                  const hiddenInput = $(this).closest('tr').find('.hidden_sell_price');
                  hiddenInput.val(sellPrice.getNumber());
                });
              }

            });
          }

          function toggleOverlay(show) {
            if (show) {
              $('#load-overlay').attr('hidden', false);
            } else {
              $('#load-overlay').attr('hidden', true);
            }
          }
        });

    </script>
@stop
