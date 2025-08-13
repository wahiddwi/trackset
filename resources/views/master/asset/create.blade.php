@extends('adminlte::page')

@section('title', 'Master Asset - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Data Master
                <small>
                    Asset
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('asset.index')}}">Master Asset</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)

@section('content')
<form action="{{ route('asset.store')}}" method="POST" enctype="multipart/form-data" id="assetFormCreate">
<div class="card">
    <div class="card-body">
            @csrf
            <input type="hidden" name="hidden_inv_cat" id="hidden_inv_cat">
            <input type="hidden" name="hidden_depresiasi" id="hidden_depreciasi">
            <input type="hidden" name="hidden_merk" id="hidden_merk">
            <input type="hidden" name="hidden_pic_type" id="hidden_pic_type">
            <input type="hidden" name="hidden_pic" id="hidden_pic">
            <input type="hidden" name="hidden_site" id="hidden_site">
            <input type="hidden" name="hidden_loc" id="hidden_loc">
            <input type="hidden" name="hidden_tag" id="hidden_tag">
            <input type="hidden" name="hidden_price" id="hidden_price">
            {{-- <input type="hidden" name="inv_company" value="{{ $selected_site }}"> --}}
            <input type="hidden" name="is_vehicle" id="isVehicle">
            <div class="row">
                <x-adminlte-input name="inv_name" label="Nama" label-class="must-fill" placeholder=""
                fgroup-class="col-md-6" error-key="inv_name" enable-old-support/>

                <x-adminlte-input name="inv_name_short" label="Short Name" label-class="must-fill" placeholder="max 30 karakter"
                fgroup-class="col-md-6" error-key="inv_name_short" enable-old-support/>
            </div>

            <div class="row">
              <x-adminlte-select2 name="inv_category" id="category" label="Kategori" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_category" class="form-control" enable-old-support>
                <option value="" selected disabled>Pilih Category</option>
                @foreach ($categories as $cat)
                    <option value="{{$cat->id}}">{{ $cat->cat_name }}</option>
                @endforeach
            </x-adminlte-select2>

              <x-adminlte-select2 name="inv_merk" id="merk" label="Merk" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_merk" class="form-control" enable-old-support>
                <option value="" selected disabled>Pilih Brand</option>
                @foreach ($brand as $br)
                    <option value="{{$br->id}}">{{ $br->brand_name }}</option>
                @endforeach
            </x-adminlte-select2>
            </div>

            <div class="row">
                {{-- <x-adminlte-input name="inv_obtaindate" label="Tanggal Perolehan" label-class="must-fill" placeholder=""
                fgroup-class="col-md-6" error-key="inv_obtaindate" type="date" enable-old-support /> --}}

              @php
                $config = [
                    'format' => 'DD MMM YYYY',
                    'dayViewHeaderFormat' => 'MMM YYYY',
                ];
              @endphp
              <x-adminlte-input-date name="inv_obtaindate" label="Tgl. Perolehan" igroup-size="md" error-key="inv_obtaindate"
                fgroup-class="col-md-6" :config="$config" value="{{ date('d M Y') }}" label-class="must-fill" enable-old-support>
                <x-slot name="appendSlot">
                  <div class="input-group-text bg-dark">
                    <i class="fas fa-calendar-day"></i>
                  </div>
                </x-slot>
              </x-adminlte-input-date>

                <x-adminlte-input name="inv_price" id="inv_price" class="price" label="Harga" label-class="must-fill" placeholder=""
                fgroup-class="col-md-6" error-key="inv_price" enable-old-support>
                </x-adminlte-input>
            </div>

            <div class="row">
                <x-adminlte-select2 name="inv_pic_type" id="inv_pic_type" label="PIC type" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_pic_type" id="picType" class="form-control" enable-old-support>
                    <option value="" selected disabled>Pilih PIC type</option>
                    <option value="user">User</option>
                    <option value="cabang">Cabang</option>
                </x-adminlte-select2>

                <x-adminlte-select2 name="inv_pic" label="PIC" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_pic" id="pic" class="form-control" enable-old-support>
                    <option value="" selected disabled>Pilih PIC</option>
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-select2 name="inv_site" id="site" label="Cabang" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_site" class="form-control">
                    <option value="" selected disabled>Pilih Cabang</option>
                    @foreach ($sites as $site)
                        <option value="{{$site->si_site}}">{{$site->si_site.' - '.$site->si_name }}</option>
                    @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="inv_loc" id="location" label="Lokasi" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_loc" class="form-control" enable-old-support>
                    <option value="" selected disabled>Pilih Lokasi</option>
                </x-adminlte-select2>
            </div>

            <div class="row">

                <x-adminlte-input name="inv_doc_ref" label="No Document Referensi" label-class="must-fill" placeholder=""
                fgroup-class="col-md-6" error-key="inv_doc_ref" enable-old-support />
                {{-- @php
                  $config = [
                        "placeholder" => "Pilih Tag",
                        "allowClear" => true,
                        ];
                @endphp
                <x-adminlte-select2 name="tag[]" id="tags" label="Tags"
                    size='sm' fgroup-class="col-md-6" :config="$config" multiple>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <i class="fas fa-tag"></i>
                        </div>
                    </x-slot>
                    @foreach ($tags as $tag)
                      <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
                    @endforeach
                </x-adminlte-select2> --}}

                <x-adminlte-select2 name="inv_tag" id="tags" label="Tag" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_tag" class="form-control" enable-old-support>
                  <option value="" selected disabled>Pilih Tag</option>
                  @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
                  @endforeach
                  <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-tag"></i>
                    </div>
                  </x-slot>
                </x-adminlte-select2>                

                <input type="hidden" name="inv_depreciation" id="depHidden" value="">
                <input type="hidden" name="inv_depreciation_value" id="depValueHidden" value="">
                <input type="hidden" name="nominal_depreciation_value" id="nominalDepHidden" value="">
            </div>

            <div class="row">
                <x-adminlte-input name="inv_sn" label="Imei / SN" label-class="must-fill" placeholder="Masukkan Imei / SN..."
                  fgroup-class="col-md-6" error-key="inv_sn" enable-old-support/>

                  <x-adminlte-input name="depreciation" id="depreciation" label="Depresiasi" placeholder=""
                  fgroup-class="col-md-6" error-key="inv_depreciation" value="" disabled/>
            </div>

            <div class="row">
              <x-adminlte-textarea name="inv_desc" label="Keterangan" fgroup-class="col-md-6" rows="3" igroup-size="sm" placeholder="Masukkan Deskripsi Asset..." enable-old-support/>
            </div>

            {{-- <div class="row">
                <x-adminlte-input name="inv_percent" id="percent" label="Percent" placeholder=""
                fgroup-class="col-md-6" error-key="inv_percant" value="" disabled>
                    <x-slot name="appendSlot">
                        <div class="input-group-text">
                            <i class="fa fa-percent"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div> --}}

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @php
                $config = [
                    'allowedFileTypes' => ['image', 'pdf'],
                    'msgInvalidFileType' => 'Jenis file "{name}" tidak valid. Hanya file "{types}" yang didukung.',
                    'browseOnZoneClick' => true,
                    'showUpload' => false,
                    'showRemove' => false,
                    'overwriteInitial' => false,
                    'dropZoneTitle' => 'Upload Dokumen Pendukung',
                    'dropZoneClickTitle' => '',
                    'fileActionSettings' => [
                        'showRemove' => true,
                        'showRotate' => false,
                        'showDrag' => false,
                        'showUpload' => false,
                    ],
                    'initialPreviewAsData' => true,
                    'maxFileSize' => 2048,
                    'maxFileCount' => 6,
                    'msgFilesTooMany' => "Jumlah file yang dipilih untuk diunggah <b>({n})</b> melebihi batas maksimum yang diperbolehkan yaitu <b>{m}</b>. Silakan coba lagi unggahan Anda!",
                    'multiple' => true,
                ];
                @endphp

                <x-adminlte-input-file-krajee name="fileUpload[]" label="Upload File" id="fileUpload" :config="$config" multiple
                accept="application/pdf,image/*" />

                <div class="row m-2">
                  <b>Note : </b> <span> Mandatory (<b class="text-danger">*</b>)</span>
                </div>

                <div class="row btn-group">
                    <x-adminlte-button class="btn" id="btn_submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                    <a href="{{route('asset.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
                </div>
            </div>
        </div>
</form>

@stop

@section('css')
    {{-- <link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous"> --}}
@endsection

@section('js')
{{-- <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/fileinput.min.js"></script> --}}
<script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>
    <script>
        $(document).ready(function () {

            // $('#inv_price').on('keyup', function () {              
            //     $('#nominalDepHidden').val(parseFloat($(this).val()));
            // })
            const amount = new AutoNumeric('#inv_price', 0, {
                currencySymbol: 'Rp. ',
                allowDecimalPadding: 'floats',
                modifyValueOnWheel: false
            });
            $('#inv_price').on('change', function () {              
              $('#hidden_price').val(amount.getNumber());
              $('#nominalDepHidden').val(amount.getNumber());
            })
            
            // console.log($('#merk').val());
            let merkValue = $('#merk').val();
            let categoryValue = $('#category').val();
            let picTypeValue = $('#inv_pic_type').val();
            
            let picValue = $('#pic').val();
            let siteValue = $('#site').val();
            let tagValue = $('#tags').val();

            $('#hidden_inv_cat').val(categoryValue);
            $('#hidden_merk').val(merkValue);
            $('#hidden_pic_type').val(picTypeValue);
            $('#hidden_pic').val(picValue);
            $('#hidden_site').val(siteValue);
            $('#hidden_tag').val(tagValue);

            $.ajax({
              type: "POST",
              url: "/asset/get-category",
              data: {
                id: categoryValue,
                _token: '{{ csrf_token() }}',
              },
              dataType: "json",
              success: function (res) {
                depElement = '';
                depCount = '';
                depLabel = '';
                percentElement = '';
                depreciationId = '';
                isVehicle = '';

                for (let i = 0; i < res.length; i++) {                            
                    var percent = res[i].cat_percent;

                    depreciationId += res[i].depreciation.id;
                    depCount += res[i].depreciation.dep_amount_periode;
                    depLabel += res[i].depreciation.dep_amount_periode == 1 ? 'Month' : 'Months';
                    isVehicle += res[i].is_vehicle;
                    if (depCount == 'null') {
                        depElement += 'No Depreciation';
                    } else {
                        depElement += depCount + ' ' + depLabel;
                    }

                    percentElement += percent;
                }

                $('#percent').val(percentElement);
                $('#depreciation').val(depElement);
                $('#depHidden').val(depreciationId);
                $('#depValueHidden').val(depCount);
                $('#isVehicle').val(isVehicle);
                $('#hidden_depresiasi').val(depElement);
              }
            });

            $.ajax({
                type: "GET",
                url: "/asset/get-type",
                dataType: "json",
                success: function (res) {
                    var users = res.user;
                    var sites = res.site;
                    var picTypeValue = $('#picType option:selected').val();

                    if (picTypeValue == "user") {
                        $('#pic option').remove();
                        $.each(users, function (i, user) {
                            $('#pic').append($('<option>', {
                                value: user.pic_nik,
                                text: user.pic_name
                            }));
                        });
                    } else if (picTypeValue == "cabang") {
                        $('#pic option').remove();
                        $.each(sites, function (i, site) {
                            $('#pic').append($('<option>', {
                                value: site.si_site,
                                text: site.si_name
                            }));
                        });
                    }

                }
            });
            

            $('#site').on('change', function () {
                var siteId = this.value;
                $('#location').html('');
                $.ajax({
                    type: "POST",
                    url: "{{ url('asset/getlocation') }}",
                    data: {
                        loc_site: siteId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function (res) {
                        $('#location').html('<option value="" selected disabled>Pilih Lokasi</option>');
                        $.each(res, function (key, val) {
                            $('#location').append('<option value="' + val.id + '">' + val.loc_name + '</option>');
                        });
                    }
                });
            });

            $('#picType').on('change', function () {
                $.ajax({
                    type: "GET",
                    url: "/asset/get-type",
                    dataType: "json",
                    success: function (res) {
                        var users = res.user;
                        var sites = res.site;
                        var picTypeValue = $('#picType option:selected').val();

                        if (picTypeValue == "user") {
                            $('#pic option').remove();
                            $.each(users, function (i, user) {
                                $('#pic').append($('<option>', {
                                    value: user.pic_nik,
                                    text: user.pic_nik + " - " + user.pic_name 
                                }));
                            });
                        } else if (picTypeValue == "cabang") {
                            $('#pic option').remove();
                            $.each(sites, function (i, site) {
                                $('#pic').append($('<option>', {
                                    value: site.si_site,
                                    text: site.si_site + " - " + site.si_name
                                }));
                            });
                        }

                    }
                });
            });

            $('#category').on('change', function () {
                var catId = this.value;
                $('#depreciation').html('');
                $.ajax({
                    type: "POST",
                    url: "/asset/get-category",
                    data: {
                        id: catId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function (res) {
                        
                        depElement = '';
                        depCount = '';
                        depLabel = '';
                        percentElement = '';
                        depreciationId = '';
                        isVehicle = '';

                        for (let i = 0; i < res.length; i++) {                            
                            var percent = res[i].cat_percent;

                            depreciationId += res[i].depreciation.id;
                            depCount += res[i].depreciation.dep_amount_periode;
                            depLabel += res[i].depreciation.dep_amount_periode == 1 ? 'Month' : 'Months';
                            isVehicle += res[i].is_vehicle;
                            if (depCount == 'null') {
                                depElement += 'No Depreciation';
                            } else {
                                depElement += depCount + ' ' + depLabel;
                            }

                            percentElement += percent;
                        }

                        $('#percent').val(percentElement);
                        $('#depreciation').val(depElement);
                        $('#depHidden').val(depreciationId);
                        $('#depValueHidden').val(depCount);
                        $('#isVehicle').val(isVehicle);

                    }
                });
            })

            $('#btn_submit').on('click', function () {

              if (!$('#inv_name').val()) {
                  $('#inv_name').addClass('is-invalid');
                  return toastr.error('Nama Asset Wajib Diisi!', 'Error');
              }
              if (!$('#inv_name_short').val()) {
                  $('#inv_name_short').addClass('is-invalid');
                  return toastr.error('Short Name Wajib Diisi!', 'Error');
              }
              if (!$('#category').val()) {
                  $('#category').addClass('is-invalid');
                  return toastr.error('Mohon pilih kategori!', 'Error');
              }
              if (!$('#merk').val()) {
                  $('#merk').addClass('is-invalid');
                  return toastr.error('Mohon Pilih Merk!', 'Error');
              }
              if (!$('#inv_obtaindate').val()) {
                  $('#inv_obtaindate').addClass('is-invalid');
                  return toastr.error('Mohon pilih tgl. perolehan!', 'Error');
              }
              if (amount.get() <= 0) {
                  $('#inv_price').addClass('is-invalid');
                  return toastr.error('Harga tidak boleh 0', 'Error');
              }
              if (!$('#site').val()) {
                  $('#site').addClass('is-invalid');
                  return toastr.error('Mohon pilih Cabang!', 'Error');
              }
              if (!$('#location').val()) {
                  $('#location').addClass('is-invalid');
                  return toastr.error('Mohon pilih Lokasi!', 'Error');
              }
              if (!$('#inv_doc_ref').val()) {
                  $('#inv_doc_ref').addClass('is-invalid');
                  return toastr.error('Dokumen Referensi Wajib Diisi!', 'Error');
              }
              if (!$('#tags').val()) {
                  $('#tags').addClass('is-invalid');
                  return toastr.error('Mohon pilih Tag!', 'Error');
              }
              if (!$('#inv_sn').val()) {
                  $('#inv_sn').addClass('is-invalid');
                  return toastr.error('Serial Number Wajib Diisi!', 'Error');
              }
              // if ($('#fileUpload').val() == '') {
              //     return toastr.error('Dokumen pendukung wajib', 'Error');
              // }

              $('#assetFormCreate').submit();

            })
        });
    </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.KrajeeFileinput', true)


