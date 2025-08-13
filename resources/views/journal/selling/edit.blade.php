@extends('adminlte::page')

@section('title', 'Selling - Edit')

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
            <form action="{{ route('selling.update', $selling->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="cust_no" id="cust_no">
                <input type="hidden" name="cust_name" id="cust_name">
                <input type="hidden" name="cust_addr" id="cust_addr">
                <input type="hidden" name="cust_telp" id="cust_telp">
                <input type="hidden" name="cust_wa" id="cust_wa">
                <input type="hidden" name="cust_email" id="cust_email">
                <input type="hidden" name="sell_company" id="sell_company" value="{{ $company->si_company }}">
                <input type="hidden" name="sell_site" id="sell_site" value="{{ $selling->site->si_site }}">

                <div class="row">
                  <x-adminlte-input name="sell_no" label="No. Transaksi" placeholder="AUTO GENERATED" igroup-size="md" fgroup-class="col-md-6" error-key="sell_no" value="{{ $selling->sell_no }}" disabled/>
                  <x-adminlte-input name="sell_transdate" label="Tgl. Transaksi" label-class="must-fill" igroup-size="md" fgroup-class="col-md-6" error-key="sell_transdate" type="date" value="{{ $selling->sell_transdate }}" />
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
                          {{ $cust->id == $selling->sell_cust_id ? 'selected' : '' }}
                        >{{ $cust->cust_name }}</option>
                    @endforeach
                  </x-adminlte-select>
                </div>
                <div class="row">
                  <x-adminlte-input name="sell_cust_name" label="Name" id="sell_cust_name" igroup-size="md" fgroup-class="col-md-6" error-key="sell_cust_name" value="{{ $selling->sell_cust_name }}" disabled/>
                  <x-adminlte-input name="sell_cust_no" label="No. Identitas" id="sell_cust_no" igroup-size="md" fgroup-class="col-md-6" error-key="sell_cust_no" value="{{ $selling->sell_cust_no }}" disabled/>
                </div>
                <div class="row">
                  <x-adminlte-textarea name="sell_cust_addr" id="sell_cust_addr" label="Alamat" igroup-size="md" fgroup-class="col-md-12" error-key="sell_cust_addr" rows=4 disabled>{{ $selling->sell_cust_addr }}</x-adminlte-textarea>
                </div>
                <div class="row">
                  <x-adminlte-input name="sell_cust_telp" label="Tel" id="sell_cust_telp" igroup-size="md" fgroup-class="col-md-4" error-key="sell_cust_telp" value="{{ $selling->sell_cust_telp }}" disabled/>
                  <x-adminlte-input name="sell_cust_wa" label="Whatsapp" id="sell_cust_wa" igroup-size="md" fgroup-class="col-md-4" error-key="sell_cust_wa" value="{{ $selling->sell_cust_wa }}" disabled/>
                  <x-adminlte-input name="sell_cust_email" label="Email" id="sell_cust_email" igroup-size="md" fgroup-class="col-md-4" error-key="sell_cust_email" value="{{ $selling->sell_cust_email }}" disabled/>
                </div>
                <div class="row">
                  <x-adminlte-textarea name="sell_desc" label="Keterangan" label-class="must-fill" igroup-size="md" fgroup-class="col-md-12" error-key="sell_desc" rows=4>{{ $selling->sell_desc }}</x-adminlte-textarea>
                </div>
                <hr>
                <div class="row">
                  {{-- <x-adminlte-input name="search" label="Scan No. Asset" id="search" igroup-size="md" fgroup-class="col-md-4" error-key="sell_asset_no"/> --}}
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
    
                {{-- <div id="newRow"></div> --}}
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
          @foreach($selling->detail as $dtl)
            editRow({{ $dtl->ordering }}, @php echo json_encode($dtl->toArray()); @endphp)
          @endforeach

            $(document).on('click', '#btnRemove', function () {
              let id = $(this).data('id');
              let transno = $('#inv_transno').val();

              assetNo.splice($.inArray(transno, assetNo), 1);

              $(this).closest('#'+ id +'').remove();
            });

            $(document).on('click', '#btnRemoveRow', function () {
              let id = $(this).data('id');
              let transno = $('#inv_transno').val();

              assetNo.splice($.inArray(transno, assetNo), 1);
              
              $(this).closest('#'+ id +'').remove();
            });

            let selected = $('#sell_cust').find(':selected');
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

          // $('#search').on('keypress', function (e) {
          //   if (e.which == 13) {
          //     e.preventDefault();
          //     search();
          //   }
          // })
          $('#search').on('change', function (e) {
            e.preventDefault();
            let search = $(this).find(':selected').val();

            if (assetNo.includes(search)) {
              return toastr.error('No. Asset sudah digunakan', 'Error');
            }
          });

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
                $('#load-overlay').attr('hidden', true);
                $('#sell_company').val(res.result.site.si_company);
                $('#sell_site').val(res.result.site.si_site);

                if (res.res == true) {
                if (res.result.inv_company != company) {
                  $('#load-overlay').attr('hidden', true);
                  return toastr.error('Penjualan harus dilakukan pada PT yang sama!', 'Error');
                }

                  let tbody = $('#selling_table tbody');
                  console.log(res);
                  
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
                            <input class="form-control price" value="${res.result.inv_current_price?res.result.inv_current_price:0}" disabled/>
                            <input type="hidden" name="dtl_asset_inv_price[]" value="${res.result.inv_current_price}">
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control price" value="${res.result.inv_current_price?res.result.inv_current_price:0}" disabled/>
                            <input type="hidden" name="hidden_sell_price[]" value="">
                          </div>
                        </div>
                      </td>
                      <td>
                        <a href="javascript:" type="button" id="btnRemove" data-id="${res.result.id}" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
                      </td>
                    </tr>`;
                  $(tr).appendTo(tbody);
                  let [price] = AutoNumeric.multiple('tr .price' , {
                    currencySymbol: 'Rp. ',
                    allowDecimalPadding: 'floats',
                  })
                } else {
                  $('#load-overlay').attr('hidden', true);
                  return toastr.error('No. Asset tidak ditemukan.', 'Error');
                }
              }
            });
          })

        });

        function editRow(row, data) {          
          let tbody = $('#selling_table tbody');
          let tr = 
            `<tr data-row="${row}" class="${row}" id="${row}">
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control inv_transno" id="inv_transno" value="${data.dtl_asset_transno}" disabled/>
                    <input type="hidden" name="dtl_asset_transno[]" value="${data.dtl_asset_transno}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control inv_name" id="inv_name" value="${data.dtl_asset_name}" disabled/>
                    <input type="hidden" name="dtl_asset_name[]" value="${data.dtl_asset_name}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control remark" id="remark" value="${data.dtl_sell_desc == null ? '' : data.dtl_sell_desc}"/>
                    <input type="hidden" name="dtl_sell_desc[]" value="${data.dtl_sell_desc}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control" value="${AutoNumeric.format(data.dtl_sell_dep_price , {
                      currencySymbol: 'Rp. ',
                      allowDecimalPadding: 'floats',
                    })}" disabled/>
                    <input type="hidden" name="dtl_asset_inv_price[]" value="${data.dtl_sell_dep_price}">
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control" value="${AutoNumeric.format(data.dtl_sell_price , {
                      currencySymbol: 'Rp. ',
                      allowDecimalPadding: 'floats',
                    })}" disabled/>
                    <input type="hidden" name="hidden_sell_price[]" value="${data.dtl_sell_price}">
                  </div>
                </div>
              </td>
              <td>
                <a href="javascript:" type="button" id="btnRemoveRow" data-id="${row}" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
              </td>
            </tr>`;
          $(tr).appendTo(tbody);
        }

    </script>
@stop
