@extends('adminlte::page')

@section('title', 'Master Kendaraan - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Kendaraan
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('vehicle.index')}}">Master Kendaraan</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)


@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <x-adminlte-select2 name="search" label="Search" placeholder="search...." id="searchValue" igroup-size="md" fgroup-class="col-md-6" error-key="search">
              <x-slot name="appendSlot">
                <div class="input-group-text text-primary bg-dark">
                <a href="javascript:" type="search" id="search"><i class="fa-brands fa-searchengine"></i> Cari!</a>
                </div>
            </x-slot>
            <option value="" selected disabled>Pilih No. Asset</option>
            @foreach ($asset as $item)
              <option value="{{$item->inv_transno}}">{{ $item->inv_transno.' - '.$item->inv_name }}</option>
            @endforeach
          </x-adminlte-select2>
        </div>
    </div>
</div>

<div class="card" id="rowVehicle" hidden>
    <div class="card-body">
      <form action="{{ route('vehicle.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="vehicle_company" id="vehicle_company" value="{{ $company->si_company }}">
        <input type="hidden" name="vehicle_status" id="vehicle_status">
        <input type="hidden" name="hidden_transno" id="hidden_transno">
        <input type="hidden" name="hidden_brand" id="hidden_brand">
        <input type="hidden" name="hidden_name" id="hidden_name">
            <div class="row">
              <x-adminlte-input name="vehicle_transno" label="No. Asset" id="vehicle_transno" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_transno" disabled readonly/>

              <x-adminlte-input name="vehicle_no" label-class="must-fill" label="No. Kendaraan" placeholder="No. Kendaraan"
              fgroup-class="col-md-6" error-key="vehicle_no" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" enable-old-support/>
            </div>
            <div class="row">
              <x-adminlte-input name="vehicle_name" label="Nama" id="vehicle_name" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_name" disabled/>

              <x-adminlte-input name="vehicle_brand" label="Brand" id="vehicle_brand" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_brand" disabled/>

              {{-- <x-adminlte-select2 name="vehicle_brand" label="Brand Kendaraan" label-class="must-fill" fgroup-class="col-md-6" error-key="vehicle_brand" class="form-control" disabled>
                <option value="" selected disabled>Pilih Brand</option>
                @foreach ($brand as $item)
                  <option value="{{ $item->id }}" {{ $item->id == $asset->merk->id ? 'selected' : ''}}>{{ $item->brand_name }}</option>                      
                @endforeach
              </x-adminlte-select2> --}}
            </div>
            <div class="row">
              <x-adminlte-input name="vehicle_documentno" label="No. STNK" label-class="must-fill" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_documentno" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" enable-old-support/>

              <x-adminlte-input name="vehicle_color" label="Warna" label-class="must-fill" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_color" enable-old-support/>
            </div>
            <div class="row">
              <x-adminlte-input name="vehicle_identityno" label="No. Rangka" label-class="must-fill" placeholder="No. Rangka"
              fgroup-class="col-md-6" error-key="vehicle_identityno" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" enable-old-support/>

              <x-adminlte-input name="vehicle_engineno" label="No. Mesin" label-class="must-fill" placeholder="No. Mesin"
              fgroup-class="col-md-6" error-key="vehicle_engineno" onkeydown="return /[a-zA-Z0-9]/i.test(event.key)" enable-old-support/>
            </div>
            <div class="row">
              <x-adminlte-input name="vehicle_capacity" label="Silinder (cc)" label-class="must-fill" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_capacity" type="number" enable-old-support/>
              
              {{-- <x-adminlte-input name="vehicle_taxdate" label="Tgl. Pajak" label-class="must-fill" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_taxdate" type="date" enable-old-support/> --}}
              @php
              $config = [
                  'format' => 'DD MMM YYYY',
                  'dayViewHeaderFormat' => 'MMM YYYY',
              ];
            @endphp
            <x-adminlte-input-date name="vehicle_taxdate" label="Tgl. Pajak" igroup-size="md" error-key="vehicle_taxdate"
              fgroup-class="col-md-6" :config="$config" value="{{ date('d M Y') }}" label-class="must-fill">
              <x-slot name="appendSlot">
                <div class="input-group-text bg-dark">
                  <i class="fas fa-calendar-day"></i>
                </div>
              </x-slot>
            </x-adminlte-input-date>
            </div>
            <div class="row">
              <x-adminlte-textarea name="vehicle_desc" label="Keterangan" fgroup-class="col-md-12" rows="4" igroup-size="sm" placeholder="Keterangan..." enable-old-support/>
            </div>
            <div class="row">
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
                      'showDrag' => true,
                      'showUpload' => true,
                  ],
                  'initialPreviewAsData' => true,
              ];
              @endphp

              <x-adminlte-input-file-krajee name="fileUpload[]" label="Upload File" id="fileUpload" :config="$config" multiple
              accept="application/pdf,image/*" fgroup-class="col-md-12" />
            </div>
            <div class="row btn-group">
              <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
              <a href="{{route('vehicle.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
      </form>
    </div>
</div>

@stop

@section('css')

<!-- default icons used in the plugin are from Bootstrap 5.x icon library (which can be enabled by loading CSS below) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.min.css" crossorigin="anonymous">
<!-- the fileinput plugin styling CSS file -->
<link href="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />

    <style>
        .btn-group {
            text-align: left;
            float: left;
            margin-top: 15px;
            margin-left: 5px;
        }
        .fileUpload {
            margin-left: 5px;
        }
    </style>
@stop

@section('js')
<script src="{{ asset('js/krajee/buffer.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/krajee/filetype.min.js') }}" type="text/javascript"></script>
<!-- the main fileinput plugin script JS file -->
<script src="{{ asset('js/krajee/fileinput.min.js') }}"></script>
<!-- sortable.min.js is only needed if you wish to sort / rearrange files in initial preview.
    This must be loaded before fileinput.min.js -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/krajee/sortable.min.js" type="text/javascript"></script>
<!-- piexif.min.js is needed for auto orienting image files OR when restoring exif data in resized images and when you
    wish to resize images before upload. This must be loaded before fileinput.min.js -->
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/krajee/piexif.min.js" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $("#fileUpload").fileinput({
                //  uploadUrl
                    uploadUrl: "/site/test-upload",
                    enableResumableUpload: true,
                    initialPreviewAsData: true,
                    allowedFileTypes: ['image', 'file'],
                    allowedFileExtensions: ["png", "jpg", "jpeg", "gif", "doc", "docx", "pdf"],
                    showCancel: true,
                    theme: 'fa5',
                    deleteUrl: '/site/file-delete',
                    fileActionSettings: {
                        showZoom: function(config) {
                            if (config.type === 'pdf' || config.type === 'image') {
                                return true;
                            }
                            return false;
                        }
                    }
                });

            $('#search').on('click', function () {
                search();
            });
        });

        function search() {
            let search = $('#searchValue').val();
            let company = $('#vehicle_company').val();
            
            $.ajax({
              type: "GET",
              url: "{{ route('vehicle.search') }}",
              data: {
                search: search,
                company: company,
              },
              success: function (res) {
                console.log(res);
                
                $('#vehicle_transno').val(res.inv.inv_transno);
                $('#hidden_transno').val(res.inv.inv_transno);
                $('#vehicle_name').val(res.inv.inv_name);
                $('#vehicle_status').val(res.inv.inv_status);
                $('#vehicle_brand').val(res.inv.merk.brand_name);
                $('#hidden_brand').val(res.inv.inv_merk);
                $('#hidden_name').val(res.inv.inv_name);

                $('#hist_polisno').val();
                $('#hist_covervalue').val();
                $('#hist_daterange').val();
                $('#hist_brand').val();
                $('#rowVehicle').attr('hidden', false);
              }
            });
        }

    </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
@section('plugins.DateRangePicker', true)
@section('plugins.TempusDominusBs4', true)
