@extends('adminlte::page')

@section('title', 'Transfer - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Transfer
                <small>
                    Asset
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('transfer.index')}}">Transfer</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)

@section('content')
<form action="{{ route('transfer.store')}}" method="POST" id="formCreateTransfer">
    @csrf
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="row">
              <input type="hidden" value="{{ $selected_company }}" id="trfCompany" name="trf_company">

              <x-adminlte-select2 name="search" label="Search" placeholder="search...." id="searchValue" igroup-size="md" fgroup-class="col-md-6" error-key="search">
                <option value="" selected disabled>Pilih No. Asset</option>
                @foreach ($asset as $item)
                  <option value="{{$item->inv_transno}}" data-name="{{ $item->inv_name }}" data-site="{{ $item->inv_site }}" data-loc="{{ $item->inv_loc }}">{{ $item->inv_transno.' - '.$item->inv_name }}</option>
                @endforeach
                <x-slot name="appendSlot">
                  <div class="input-group-text text-primary bg-dark">
                  <a href="javascript:" type="search" id="search"><i class="fa-brands fa-searchengine"></i> Go!</a>
                  </div>
              </x-slot>
            </x-adminlte-select2>
            </div>
          </div>
        </div>
        <div class="row">
          <x-adminlte-input name="trf_transno" label="No. Transfer" placeholder="AUTO GENERATE"
          fgroup-class="col-md-6" error-key="trf_transno" disabled/>
          
        @php
          $config = [
              'format' => 'DD MMM YYYY',
              'dayViewHeaderFormat' => 'MMM YYYY',
          ];
        @endphp
        <x-adminlte-input-date name="trf_transdate" label="Tgl. Transfer" igroup-size="md" error-key="trf_transdate"
          fgroup-class="col-md-6" :config="$config" value="{{ date('d M Y') }}" label-class="must-fill">
          <x-slot name="appendSlot">
            <div class="input-group-text bg-dark">
              <i class="fas fa-calendar-day"></i>
            </div>
          </x-slot>
        </x-adminlte-input-date>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">Cabang & Lokasi Asal</div>
              <div class="card-body">
                <input type="hidden" name="trf_site_from" id="trf_site_from">
                <input type="hidden" name="trf_loc_from" id="trf_loc_from">
                <input type="hidden" name="trf_pic_type_from" id="trf_pic_type_from">
                <input type="hidden" name="trf_pic_from" id="trf_pic_from">

                <div class="row">
                  <x-adminlte-input name="trf_site" label="Cabang Asal" placeholder=""
                  fgroup-class="col-md-6" error-key="trf_site_from" id="siteFrom" disabled/>

                  <x-adminlte-input name="trf_loc_from" label="Lokasi Asal" placeholder=""
                  fgroup-class="col-md-6" error-key="trf_loc_from" id="locFrom" disabled/>
                </div>

                <div class="row">
                  <x-adminlte-input name="pic_type_from" label="Tipe PIC Asal" placeholder=""
                  fgroup-class="col-md-6" error-key="pic_type_from" id="picTypeFrom" disabled/>

                  <x-adminlte-input name="trf_pic" label="PIC Asal" placeholder=""
                  fgroup-class="col-md-6" error-key="trf_pic" id="picFrom" disabled/>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">Cabang & Lokasi Tujuan</div>
              <div class="card-body">
                <input type="hidden" name="trf_site_to" id="trf_site_to">
                <input type="hidden" name="trf_loc_to" id="trf_loc_to">
                <input type="hidden" name="trf_pic_type_to" id="trf_pic_type_to">
                <input type="hidden" name="trf_pic_to" id="trf_pic_to">

                <span class="text-danger text-center" style="text-align: center;" fgroup-class="col-md-6" id="errorMsg"></span>
                <div class="row">
                  <x-adminlte-select2 name="trf_site_to" id="siteTo" label="Cabang Tujuan" label-class="must-fill" fgroup-class="col-md-6" error-key="trf_site_to" class="form-control">
                    <option value="" selected disabled>Pilih Cabang Tujuan</option>
                    @foreach ($sites as $site)
                    <option value="{{$site->si_site}}">{{$site->si_site.' - '.$site->si_name }}</option>
                    @endforeach
                  </x-adminlte-select2>
    
                  <x-adminlte-select2 name="trf_loc_to" id="locTo" label="Lokasi Tujuan" label-class="must-fill" fgroup-class="col-md-6" error-key="trf_loc_to" class="form-control">
                      <option value="" selected disabled>Pilih Lokasi Tujuan</option>
                  </x-adminlte-select2>
                </div>
                <div class="row">
                  <x-adminlte-select name="pic_type_to" label="Tipe PIC Tujuan" label-class="must-fill" fgroup-class="col-md-6" error-key="pic_type_to" id="picTypeTo" class="form-control">
                    <option value="" selected disabled>Pilih PIC type Tujuan</option>
                    <option value="user">User</option>
                    <option value="cabang">Cabang</option>
                  </x-adminlte-select>
    
                  <x-adminlte-select2 name="trf_pic_to" label="PIC Tujuan" label-class="must-fill" fgroup-class="col-md-6" error-key="trf_pic_to" id="picTo" class="form-control">
                      <option value="" selected disabled>Pilih PIC Tujuan</option>
                  </x-adminlte-select2>
                </div>
                <div class="row">
                  <div class="col-md-12 text-right">
                    <x-adminlte-button class="btn" id="btn_generate" label="Generate Transfer" theme="primary" icon="fas fa-lg fa-solid fa-plus"/>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <x-adminlte-textarea name="trf_desc" label="Keterangan" fgroup-class="col-md-12" rows="5" igroup-size="sm" placeholder="Masukkan Deskripsi Asset..."/>
        </div>
        {{-- table for accomodate selected items --}}
        <div class="row">
          <table class="table table-striped" id="assetTable">
              <thead>
                  <tr>
                    <th>No. Asset</th>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                  </tr>
              </thead>
              <tbody></tbody>
          </table>
        </div>
      <div class="card-footer">
        <div class="row btn-group btnsubmit">
            <x-adminlte-button class="btn" id="btn_submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
            <a href="{{route('transfer.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
        </div>
      </div>
      <div class="overlay" id="load-overlay" hidden>
          <i class="fas fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
</form>
@stop

@section('css')

@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <script>
      $(document).ready(function () {
        $('#siteTo').on('change', function () {
          let siteToId = $(this).val();
          if (siteToId) {
            $.ajax({
              type: "GET",
              url: "{{ route('transfer.getlocation') }}",
              data: {
                site_id: siteToId,
              },
              success: function (res) {
                $('#locTo').empty();
                $('#locTo').append('<option value="" selected disabled>Pilih Lokasi Tujuan</option>');

                $.each(res, function(i, loc) {
                    $('#locTo').append('<option value="' + loc.id + '">' + loc.loc_id + ' - ' + loc.loc_name + '</option>');
                });
              },
              error: function (xhr) {
                console.log(xhr.responseText);
                toastr.error('Transfer Asset gagal, silahkan coba lagi.', 'Error!');
              }
            });
          }              
        });
        
        $('#picTypeTo').on('change', function () {
          let picTypeTo = $(this).val();
          let siteTo = $('#siteTo').val();

          // Cabang Tujuan harus dipilih
          if (!siteTo) {
              toastr.error('Pilih Cabang Tujuan terlebih dahulu.', 'Error');
              $(this).val('').trigger('change');
              return;
          }
          
            $.ajax({
                type: "GET",
                url: "/transfer/get-type",
                dataType: "json",
                success: function (res) {
                  let users = res.user;
                  let sites = res.site;
                  let picSelect = $('#picTo');

                  picSelect.empty().append('<option value="" selected disabled>Pilih PIC Tujuan</option>');

                  if (picTypeTo == "user") {
                    // Jika pilih User, tampilkan semua user yang tersedia
                    users.forEach(user => {
                      picSelect.append(`<option value="${user.pic_nik}">${user.pic_nik} - ${user.pic_name}</option>`);
                    });

                  } else if (picTypeTo == "cabang") {
                      // Jika pilih Cabang, hanya tampilkan PIC yang sesuai dengan Cabang Tujuan
                      sites.forEach(site => {
                        if (site.si_site === siteTo) {
                          picSelect.append(`<option value="${site.si_site}">${site.si_site} - ${site.si_name}</option>`);
                        }
                      });
                  }
                  
                  picSelect.trigger('change');
                  
                },
                error: function () {
                  toastr.error('Gagal mengambil data PIC.', 'Error');
                }
            });
        });

        let isLocked = false;
        // search asset
        $('#search').on('click', function (e) {
            e.preventDefault();
            let searchValue = $('#searchValue').val();
            let company = $('#trfCompany').val();

            if (!searchValue) {
                return toastr.error('Silakan masukkan nomor aset.', 'Error');
            }

            let isDuplicate = false;
            $('#assetTable tbody tr').each(function () {
                let existingAsset = $(this).find('td input[name="trfdtl_asset_no[]"]').val();
                if (existingAsset === searchValue) {
                    isDuplicate = true;
                    return false;
                }
            });

            if (isDuplicate) {
                return toastr.error('Nomor asset ini sudah ditambahkan.', 'Error');
            }

            searchAsset(searchValue, company);
        });

        let selectedSite = null;
        // generate transfer
        $('#btn_generate').on('click', function (e) {
          e.preventDefault();

          let siteTo = $('#siteTo').val();
          let locTo = $('#locTo').val();
          let picTypeTo = $('#picTypeTo').val();
          let picTo = $('#picTo').val();
          let assetNumber = $('#searchValue').val();
          let assetName = $("#searchValue option:selected").data('name');
          let assetSite = $("#searchValue option:selected").data('site');
          selectedSite = assetSite;

          // validation column destination must fill
          if (!siteTo || !locTo || !picTypeTo || !picTo || !assetNumber) {
              toastr.error('Semua kolom tujuan harus dipilih.', 'Error');
              return;
          }

          let isDuplicate = false;
          $('#assetTable tbody tr').each(function () {
            let existingAsset = $(this).find('td input[name="trfdtl_asset_no[]"]').val();
            
            if (existingAsset === assetNumber) {
              isDuplicate = true;
              return false;
            }
          });

          if (isDuplicate) {
            toastr.error('Asset ini sudah ditambahkan!','Error');
            return;
          }

          let rowNumber = $('#assetTable tbody tr').length + 1;

          // payload sito_to, loc_to, pic_type_to, pic_to
          $('#trf_site_to').val(siteTo);
          $('#trf_loc_to').val(locTo);
          $('#trf_pic_type_to').val(picTypeTo);
          $('#trf_pic_to').val(picTo);

          let newRow = `
            <tr data-row="${rowNumber}">
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control numer" value="${assetNumber}" disabled/>
                    <input  type="hidden" name="trfdtl_asset_no[]" value="${assetNumber}"/>
                    <input  type="hidden" name="trfdtl_order[]" value="${rowNumber}"/>
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control name" value="${assetName}" disabled/>
                    <input  type="hidden" name="trfdtl_asset_name[]" value="${assetName}"/>
                  </div>
                </div>
              </td>
              <td>
                <div class="form-group">
                  <div class="input-group">
                    <input class="form-control remark" value="" name="trfdtl_desc[]"/>
                  </div>
                </div>
              </td>
              <td>
                <a href="javascript:" type="button" id="btnRemove" class="btn btn-danger btn-sm btn-remove"><i class="fa-solid fa-trash"></i></a>
              </td>
            </tr>
          `;
          $('#assetTable tbody').append(newRow);

          // after added first asset, lock cabang & location destination
          if (!isLocked) {
            $('#siteTo').prop('disabled', true);
            $('#locTo').prop('disabled', true);
            $('#picTypeTo').prop('disabled', true);
            $('#picTo').prop('disabled', true);
            isLocked = true;
            $('#btn_generate').hide();

            updateDropdowns(selectedSite);
          }

        });

        function updateDropdowns(siteFrom) {
          $('#searchValue').empty().append('<option value="" selected disabled>Pilih No. Asset</option>');
          
          $.ajax({
            type: "GET",
            url: "{{ route('transfer.get-assets-by-site') }}",
            data: {
              site_from: siteFrom,
            },
            success: function (res) {
              if (res.res) {                
                res.data.forEach(function (item) {
                  $('#searchValue').append(
                      `<option value="${item.inv_transno}" data-name="${item.inv_name}" data-site="${item.inv_site}" data-loc="${item.inv_loc}">
                          ${item.inv_transno} - ${item.inv_name}
                      </option>`
                    );
                });
              }
            },
            error: function (xhr) {
              console.error('Gagal mengambil data aset.');
            }
          });
        }

        function searchAsset(data, company) {
          let siteFrom = $('#trf_site_from').val();

          $.ajax({
              type: "GET",
              url: "{{ route('transfer.search') }}",
              data: { 
                search: data,
                company: company,
                site_from: siteFrom
              },
              success: function (res) {   
                  if (res.res) {
                    if (res.data.inv_company != company) {
                      return toastr.error('Transfer tidak dapat dilakukan antar PT.', 'Error');
                    }

                    let assetNumber = res.data.inv_transno;
                    let assetName = res.data.inv_name;
                    
                    // showing asset from
                    $('#siteFrom').val(res.data.site.si_name);
                    $('#locFrom').val(res.data.location.loc_name);
                    $('#picTypeFrom').val(res.data.inv_pic_type.toUpperCase());
                    $('#picFrom').val(res.data.pic ? res.data.pic.pic_name : res.data.site.si_name);

                    if ($('#assetTable tbody tr').length === 0) {                        
                      $('#trf_site_from').val(res.data.site.si_site);
                      $('#trf_loc_from').val(res.data.location.id);
                      $('#trf_pic_type_from').val(res.data.inv_pic_type);
                      $('#trf_pic_from').val(res.data.pic ? res.data.pic.pic_nik : res.data.site.si_site);
                    }

                    if (isLocked) {
                        let assetNumber = res.data.inv_transno;
                        let assetName = res.data.inv_name;

                        let isDuplicate = false;
                        $('#assetTable tbody tr').each(function () {
                            let existingAsset = $(this).find('td input[name="trfdtl_asset_no[]"]').val();
                            if (existingAsset === assetNumber) {
                                isDuplicate = true;
                                return false;
                            }
                        });

                        if (isDuplicate) {
                            return toastr.error('Nomor asset ini sudah ditambahkan.', 'Error');
                        }

                        let rowNumber = $('#assetTable tbody tr').length + 1;

                        let newRow = `
                            <tr data-row="${rowNumber}">
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control numer" value="${assetNumber}" disabled/>
                                            <input type="hidden" name="trfdtl_asset_no[]" value="${assetNumber}"/>
                                            <input type="hidden" name="trfdtl_order[]" value="${rowNumber}"/>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control name" value="${assetName}" disabled/>
                                            <input type="hidden" name="trfdtl_asset_name[]" value="${assetName}"/>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control remark" value="" name="trfdtl_desc[]"/>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="javascript:" type="button" id="btnRemove" class="btn btn-danger btn-sm btn-remove">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                        $('#assetTable tbody').append(newRow);
                    }

                  } else {
                      toastr.error('No. Asset tidak ditemukan.', 'Error');
                  }
              }
          });
        }
        

        // remove aset    
        $('#assetTable').on('click', '.btn-remove', function () {
          $(this).closest('tr').remove();

          $('#assetTable tbody tr').each(function (i) {
            $(this).attr('data-row', i+1);
          })

          // if all asset removed, reopen Cabang dan location destination
          if ($('#assetTable tbody tr').length === 0) {
              $('#siteTo').prop('disabled', false);
              $('#locTo').prop('disabled', false);
              $('#picTypeTo').prop('disabled', false);
              $('#picTo').prop('disabled', false);
              isLocked = false;
              let selectedSite = null;
              $('#siteFrom').val('');
              $('#locFrom').val('');
              $('#picTypeFrom').val('');
              $('#picFrom').val('');
              resetSelect();

              $('#btn_generate').show();
          }
        });

        function resetSelect() {
          let company = $('#trfCompany').val();
          $('#trf_site_from').val('')
          $('#searchValue').empty().append('<option value="" selected disabled>Pilih No. Asset</option>');

          $.ajax({
            type: "GET",
            url: "{{ route('transfer.reset-select-site') }}",
            data: {
              company: company
            },
            success: function (res) {
              console.log(res);
              if (res.res) {
                res.data.forEach(function (item) {
                  $('#searchValue').append(
                      `<option value="${item.inv_transno}" data-name="${item.inv_name}" data-site="${item.inv_site}" data-loc="${item.inv_loc}">
                          ${item.inv_transno} - ${item.inv_name}
                      </option>`
                    );
                });
              }
            }
          });
        }

        $('#btn_submit').on('click', function (e) {
          e.preventDefault();

          if ($('#assetTable tbody tr').length === 0) {
              toastr.error('Tidak ada asset yang di transfer!', 'Error');
              return;
          }

          $('#formCreateTransfer').submit();
        });
      });
    </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
@section('plugins.TempusDominusBs4', true)
