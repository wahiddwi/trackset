@extends('adminlte::page')

@section('title', 'Disposal - Create')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                  Disposal
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
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('disposal.store') }}" method="POST">
                @csrf
                <input type="hidden" name="dis_company" id="dis_company" value="{{ $selected_company }}">
                <input type="hidden" name="dis_site" id="dis_site">

                <div class="row">
                  <x-adminlte-input name="dis_no" label="No. Transaksi" placeholder="AUTO GENERATED" igroup-size="md" fgroup-class="col-md-6" error-key="dis_no" disabled/>
                  {{-- <x-adminlte-input name="dis_transdate" label="Tgl. Transaksi" label-class="must-fill" igroup-size="md" fgroup-class="col-md-6" error-key="dis_transdate" type="date" /> --}}
                  @php
                    $config = [
                        'format' => 'DD MMM YYYY',
                        'dayViewHeaderFormat' => 'MMM YYYY',
                    ];
                  @endphp
                  <x-adminlte-input-date name="dis_transdate" id="dis_transdate" label="Tgl. Transaksi" igroup-size="md" error-key="dis_transdate"
                    fgroup-class="col-md-6" :config="$config" value="{{ date('d M Y') }}" label-class="must-fill" enable-old-support>
                    <x-slot name="appendSlot">
                      <div class="input-group-text bg-dark">
                        <i class="fas fa-calendar-day"></i>
                      </div>
                    </x-slot>
                  </x-adminlte-input-date>
                </div>
                <div class="row">
                  <x-adminlte-textarea name="dis_desc" label="Keterangan" label-class="must-fill" igroup-size="md" fgroup-class="col-md-12" error-key="dis_desc" rows=4/>
                </div>
                <hr>
                <div class="row">
                  <x-adminlte-select2 name="dis_asset_no" id="search" label="Pilih No. Asset" fgroup-class="col-md-6" error-key="dis_asset_no" class="form-control">
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
    
                <table id="disposal_table" class="table table-bordered display responsive nowrap" style="width: 100%">
                  <thead>
                      <tr>
                          <th>No. Asset</th>
                          <th>Barang</th>
                          <th>Keterangan</th>
                          <th></th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
    
                {{-- <div id="newRow"></div> --}}
                <div class="row btn-group">
                    <x-adminlte-button class="btn" type="submit" id="btn_submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                    <a href="{{route('disposal.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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
                // $(this).closest('').remove();
                let id = $(this).data('id');
                let transno = $('#inv_transno').val();

                assetNo.splice($.inArray(transno, assetNo), 1);

                $(this).closest('#'+ id +'').remove();
            });

          $('#search').on('change', function (e) {
            e.preventDefault();
            let search = $(this).find(':selected').val();
            
            // console.log(search);
            // if (assetNo.includes(search)) {
            //   return toastr.error('No. Asset sudah digunakan', 'Error');
            // }
          })

          $('#btn-search').on('click', function (e) {
            e.preventDefault();
            $('#load-overlay').removeAttr('hidden');

            let search = $('#search').val();
            let company = $('#dis_company').val();
            

            if (assetNo.includes(search)) {
              $('#load-overlay').attr('hidden', true);
              return toastr.error('No. Asset sudah digunakan', 'Error');
            }

            $.ajax({
              type: "GET",
              url: "{{ route('disposal.search') }}",
              data: {
                search: search,
                company: company,
              },
              dataType: "json",
              success: function (res) {
                
                assetNo.push(search);
                $('#load-overlay').attr('hidden', true);
                $('#dis_company').val(res.result.site.si_company);
                // $('#dis_site').val(res.result.site.si_site);
                
                if (res.res == true) {
                if (res.result.inv_company != company) {
                  $('#load-overlay').attr('hidden', true);
                  return toastr.error('Penjualan Harus dilakukan pada PT yang sama', 'Error');
                }

                let line = $('#disposal_table tbody tr').length + 1;
                

                  let tbody = $('#disposal_table tbody');                  
                  let tr = 
                    `<tr class="${res.result.id}" id="${res.result.id}">
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control inv_transno" id="inv_transno" value="${res.result.inv_transno}" disabled/>
                            <input type="hidden" name="disdtl_asset_transno[]" value="${res.result.inv_transno}">
                            <input type="hidden" name="disdtl_asset_site[]" value="${res.result.inv_site}">
                            <input type="hidden" name="disdtl_order[]" value="${line}">
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control name" value="${res.result.inv_name}" disabled/>
                            <input value="${res.result.inv_name}" type="hidden" name="disdtl_asset_name[]"/>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="form-group">
                          <div class="input-group">
                            <input class="form-control name" value="" name="disdtl_desc[]"/>
                          </div>
                        </div>
                      </td>
                      <td>
                        <a href="javascript:" type="button" id="btnRemove" data-id="${res.result.id}" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
                      </td>
                    </tr>`;
                  $(tr).appendTo(tbody);
                } else {
                  $('#load-overlay').attr('hidden', true);
                  return toastr.error('No. Asset tidak ditemukan.', 'Error');
                }
                
              }
            });
          });
        });

    </script>
@stop
