@extends('adminlte::page')

@section('title', 'Master Asset - Edit')

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
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)
@section('plugins.KrajeeFileinput', true)


@section('content')
<form action="{{ route('asset.update', $asset->id)}}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
<div class="card">
    <div class="card-body">
            @csrf
            <input type="hidden" name="is_vehicle" id="isVehicle" value="{{ $asset->is_vehicle }}">
            <input type="hidden" name="inv_company" value="{{ $asset->inv_company }}">
            <input type="hidden" name="inv_status" value="{{ $asset->inv_status }}">
            <input type="hidden" name="inv_depreciation_value" id="inv_depreciation_value" value="{{ $asset->category->depreciation->dep_amount_periode }}">
            <input type="hidden" name="inv_obtaindate" id="hidden_obtaindate" value="{{ $asset->inv_obtaindate }}">
            <input type="hidden" name="hidden_price" id="hidden_price" value="{{ $asset->inv_price }}">

            {{-- <input type="hidden" name="is_vehicle" id="isVehicle"> --}}
            <div class="row">
                <x-adminlte-input name="inv_name" label="Nama" placeholder="" label-class="must-fill"
                fgroup-class="col-md-6" error-key="inv_name" value="{{ $asset->inv_name }}"/>

                <x-adminlte-input name="inv_name_short" label="Short Name" placeholder="max. 30 karakter" label-class="must-fill"
                fgroup-class="col-md-6" error-key="inv_name_short" value="{{ $asset->inv_name_short }}"/>
            </div>
            <div class="row">
              @if ($asset->inv_status == 'ONHAND')
                <x-adminlte-input name="inv_category_show" label="Kategori" placeholder=""
                fgroup-class="col-md-6" error-key="inv_category" value="{{ $asset->category->cat_name }}" disabled readonly/>

                <input type="hidden" name="inv_category" value="{{ $asset->inv_category }}">
              @else
                <x-adminlte-select2 name="inv_category" id="category" label="Kategori" fgroup-class="col-md-6" error-key="inv_category" class="form-control" label-class="must-fill">
                  @foreach ($categories as $cat)
                      <option value="{{$cat->id}}" {{ $asset->inv_category == $cat->id ? 'selected' : ''}}>{{ $cat->cat_name }}</option>
                  @endforeach
                </x-adminlte-select2>
              @endif

              <x-adminlte-select2 name="inv_merk" id="merk" label="Merk" fgroup-class="col-md-6" error-key="inv_merk" class="form-control" label-class="must-fill">
                <option value="" {{ $asset->inv_merk == null ? 'selected disabled' : '' }} >Pilih Merk</option>
                @foreach ($brands as $brand)
                  <option value="{{$brand->id}}" {{ $asset->inv_merk == $brand->id ? 'selected' : ''}}>{{ $brand->brand_name }}</option>
                @endforeach
            </x-adminlte-select2>
            </div>


            {{-- @if ($asset->inv_status == 'ONHAND')
              <div class="row">
                <input type="hidden" name="inv_obtaindate" value="{{ $asset->inv_obtaindate }}">
                <input type="hidden" name="inv_price" value="{{ $asset->inv_price }}">
                <x-adminlte-input name="inv_obtaindate_show" label="Tanggal Perolehan" placeholder=""
                fgroup-class="col-md-6" error-key="inv_obtaindate" value="{{ Carbon\Carbon::parse($asset->inv_obtaindate)->format('d M Y') }}" disabled readonly/>
                

                <x-adminlte-input name="inv_price_show" label="Harga" placeholder=""
                fgroup-class="col-md-6" error-key="inv_price" value="{{ 'Rp. '.number_format($asset->inv_price, 0) }}" disabled readonly/>
              </div>

              <div class="row">
                <x-adminlte-select name="inv_pic_type_show" label="PIC type" fgroup-class="col-md-6" error-key="inv_pic_type" id="picType" class="form-control" disabled readonly>
                    @if ($asset->inv_pic_type == "user")
                        <option value="{{ $asset->inv_pic_type }}" selected>{{ Str::ucfirst($asset->inv_pic_type) }}</option>
                        <option value="cabang">Cabang</option>
                    @else
                        <option value="{{ $asset->inv_pic_type }}" selected>{{ Str::ucfirst($asset->inv_pic_type) }}</option>
                        <option value="user">User</option>
                    @endif
                </x-adminlte-select>
                <input type="hidden" id="inv_pic_type" name="inv_pic_type" value="{{ $asset->inv_pic_type }}">
                <x-adminlte-select2 name="inv_pic_show" label="PIC" fgroup-class="col-md-6" error-key="inv_pic" id="pic" class="form-control" disabled readonly>
                    @foreach ($users as $user)
                        <option value="{{ $asset->inv_pic }}" {{ $asset->inv_pic == $user->pic_nik ? 'selected' : ''}}>{{ $user->pic_nik ." - ". $user->pic_name }}</option>
                    @endforeach
                </x-adminlte-select2>
                <input type="hidden" name="inv_pic" value="{{ $asset->inv_pic }}">
              </div>

              <div class="row">
                  <x-adminlte-select2 name="inv_site_show" id="site" label="Cabang" fgroup-class="col-md-6" error-key="inv_site" class="form-control" disabled readonly>
                      <option value="" selected disabled>Pilih Cabang</option>
                      @foreach ($sites as $site)
                          <option value="{{$site->si_site}}" {{ $asset->inv_site == $site->si_site ? 'selected' : ''}}>{{ $site->si_site ." - ". $site->si_name }}</option>
                      @endforeach
                  </x-adminlte-select2>

                  <input type="hidden" name="inv_site" value="{{ $asset->inv_site }}">

                  <x-adminlte-select2 name="inv_loc_show" id="location" label="Lokasi" fgroup-class="col-md-6" error-key="inv_loc" class="form-control" disabled readonly>
                      @foreach ($locations as $loc)
                              <option value="{{ $loc->id }}" {{ $loc->id == $asset->inv_loc ? 'selected' : '' }}>{{ $loc->loc_name }}</option>
                      @endforeach
                  </x-adminlte-select2>

                  <input type="hidden" name="inv_loc" value="{{ $asset->inv_loc}}">
              </div>
            @else --}}
              <div class="row">           
                @php
                $config = [
                    'format' => 'DD MMM YYYY',
                    'dayViewHeaderFormat' => 'MMM YYYY',
                ];
              @endphp
              <x-adminlte-input-date name="inv_obtaindate_show" id="inv_obtaindate_show" label="Tgl. Perolehan" igroup-size="md" error-key="inv_obtaindate"
                fgroup-class="col-md-6" :config="$config" value="{{ Carbon\Carbon::parse($asset->inv_obtaindate)->format('d M Y') }}" label-class="must-fill" enable-old-support>
                <x-slot name="appendSlot">
                  <div class="input-group-text bg-dark">
                    <i class="fas fa-calendar-day"></i>
                  </div>
                </x-slot>
              </x-adminlte-input-date>

                <x-adminlte-input name="inv_price" id="inv_price" label="Harga" label-class="must-fill" placeholder=""
                  fgroup-class="col-md-6" error-key="inv_price" value="{{ $asset->inv_price }}">
                </x-adminlte-input>

              </div>

              <div class="row">
                  <x-adminlte-select name="inv_pic_type" label="PIC type" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_pic_type" id="picType" class="form-control">
                      @if ($asset->inv_pic_type == "user")
                          <option value="{{ $asset->inv_pic_type }}" selected>{{ Str::ucfirst($asset->inv_pic_type) }}</option>
                          <option value="cabang">Cabang</option>
                      @else
                          <option value="{{ $asset->inv_pic_type }}" selected>{{ Str::ucfirst($asset->inv_pic_type) }}</option>
                          <option value="user">User</option>
                      @endif
                  </x-adminlte-select>

                  <x-adminlte-select2 name="inv_pic" label="PIC" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_pic" id="pic" class="form-control">
                      @if ($asset->inv_pic_type == 'user')
                        @foreach ($users as $user)
                            <option value="{{ $user->pic_nik }}" {{ $asset->inv_pic == $user->pic_nik ? 'selected' : ''}}>{{ $user->pic_name }}</option>
                        @endforeach
                      @else
                        @foreach ($sites as $site)
                            <option value="{{ $site->si_site }}" {{ $asset->inv_pic == $site->si_site ? 'selected' : ''}}>{{ $site->si_name }}</option>
                        @endforeach
                      @endif
                  </x-adminlte-select2>
              </div>

              <div class="row">
                  <x-adminlte-select2 name="inv_site" id="site" label="Cabang" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_site" class="form-control">
                      <option value="" selected disabled>Pilih Cabang</option>
                      @foreach ($sites as $site)
                          <option value="{{$site->si_site}}" {{ $asset->inv_site == $site->si_site ? 'selected' : ''}}>{{ $site->si_name }}</option>
                      @endforeach
                  </x-adminlte-select2>

                  <x-adminlte-select2 name="inv_loc" id="location" label="Lokasi" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_loc" class="form-control">
                      @foreach ($locations as $loc)
                              <option value="{{ $loc->id }}" {{ $loc->id == $asset->inv_loc ? 'selected' : '' }}>{{ $loc->loc_name }}</option>
                      @endforeach
                  </x-adminlte-select2>
              </div>
            {{-- @endif --}}
            <div class="row">
                <x-adminlte-input name="inv_doc_ref" label-class="must-fill" label="No Document Referensi" placeholder=""
                fgroup-class="col-md-6" error-key="inv_doc_ref" value="{{ $asset->inv_doc_ref }}"/>
              
                <x-adminlte-select2 name="inv_tag" id="tags" label="Tag" label-class="must-fill" fgroup-class="col-md-6" error-key="inv_tag" class="form-control">
                  <x-slot name="prependSlot">
                    <div class="input-group-text">
                        <i class="fas fa-tag"></i>
                    </div>
                  </x-slot>
                  <option value="" {{ $asset->inv_tag == null ? 'selected disabled' : '' }} >Pilih Tag</option>
                  @foreach ($tags as $tag)
                    <option value="{{$tag->id}}" {{ $asset->inv_tag == $tag->id ? 'selected' : ''}}>{{ $tag->tag_name }}</option>
                  @endforeach
                </x-adminlte-select2>  

                <input type="hidden" name="inv_depreciation" id="depHidden" value="{{ $asset->inv_depreciation }}">
                {{-- <input type="hidden" name="inv_depreciation_value" id="depValueHidden" value="{{ $asset->inv_dep_periode }}"> --}}
                {{-- <input type="hidden" name="nominal_depreciation_value" id="nominalDepHidden" value="{{ $asset->inv_nominal_dep }}"> --}}
            </div>

            <div class="row">
                <x-adminlte-input name="inv_sn" label="Imei / SN" label-class="must-fill" placeholder="Masukkan Imei / SN..."
                  fgroup-class="col-md-6" error-key="inv_sn" value="{{ $asset->inv_sn }}"/>

                <x-adminlte-input name="depreciation" id="depreciation" label="Depresiasi" placeholder=""
                  fgroup-class="col-md-6" error-key="inv_depreciation" value="{{ $asset->category->depreciation->dep_amount_periode }}" disabled/>
            </div>

            <div class="row">
              <x-adminlte-textarea name="inv_desc" label="Keterangan" fgroup-class="col-md-6" rows="3" igroup-size="sm" placeholder="Masukkan Deskripsi Asset...">{{ $asset->inv_desc }}</x-adminlte-textarea>
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
                    'uploadUrl' => route('asset.file-upload'),
                    'uploadAsync' => true,
                    'deleteUrl' => route('asset.file-delete'),
                    'uploadExtraData' => ['_token' => csrf_token(), 'match_id' => $asset->id],
                    'deleteExtraData' => ['_token' => csrf_token(), 'match_id' => $asset->id],
                    'initialPreviewAsData' => true,
                    'initialPreview' => $uploadedFiles,
                    'initialPreviewConfig' => $uploadConfigs,
                ];
                @endphp

                <x-adminlte-input-file-krajee name="fileUpload" id="fileUpload" label="File Upload" label-class="must-fill" :config="$config" multiple
                accept="application/pdf,image/*" />

                <div class="row btn-group">
                    <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                    <a href="{{route('asset.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
                </div>
            </div>
        </div>
</form>

@stop

@section('css')
@endsection

@section('js')
<script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#fileUpload').on('filebatchselected', function(event, files) {
                $(this).fileinput('upload');
            });

            const price = new AutoNumeric('#inv_price', {{ $asset->inv_price }}, {
              minimumValue: 0,
              currencySymbol: 'Rp. ',
              allowDecimalPadding: 'floats',
              modifyValueOnWheel: false,
            });

            $('#inv_price').on('change', function() {
              $('#hidden_price').val(price.get());
            })

            $('#inv_category_show').val();

            $('#inv_price').on('keyup', function () {
                $('#nominalDepHidden').val(parseFloat($(this).val()));
            })

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
                            isVehicle += res[i].is_vehicle;

                            depreciationId += res[i].depreciation.id;
                            depCount += res[i].depreciation.dep_amount_periode;
                            depLabel += res[i].depreciation.dep_amount_periode == 1 ? 'Month' : 'Months';
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
                        $('#isVehicle').val(isVehicle ? true : false);

                    }
                });
            })
        });
    </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
@section('plugins.TempusDominusBs4', true)

