@extends('adminlte::page')

@section('title', 'Master Kendaraan - Detail')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Detail Data
                <small>
                    Kendaraan
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('vehicle.index')}}">Kendaraan</a></li>
                    <li class="breadcrumb-item active">Edit</li>
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
      <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" id="nav-vehicle-tab" data-toggle="tab" href="#nav-vehicle" role="tab" aria-controls="nav-vehicle" aria-selected="true">Kendaraan</a>
          <a class="nav-item nav-link" id="nav-insurance-tab" data-toggle="tab" href="#nav-insurance" role="tab" aria-controls="nav-insurance" aria-selected="false">Asuransi</a>
        </div>
      </nav>
      <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane p-3 fade show active" id="nav-vehicle" role="tabpanel" aria-labelledby="nav-vehicle-tab">
          <div class="row">
            <x-adminlte-input name="vehicle_transno" label="No. Asset" id="vehicle_transno" placeholder=""
            fgroup-class="col-md-6" error-key="vehicle_transno" value="{{ $vehicle->vehicle_transno }}" disabled readonly/>

            <x-adminlte-input name="vehicle_no" label="No. Kendaraan" placeholder="No. Kendaraan"
            fgroup-class="col-md-6" error-key="vehicle_no" value="{{ $vehicle->vehicle_no }}" disabled readonly />
          </div>
          <div class="row">
            <x-adminlte-input name="vehicle_name" label="Nama" id="vehicle_name" placeholder=""
            fgroup-class="col-md-6" error-key="vehicle_name" value="{{ $vehicle->vehicle_name }}" disabled readonly />

            <x-adminlte-select2 name="vehicle_brand" label="Brand Kendaraan" fgroup-class="col-md-6" error-key="vehicle_brand" class="form-control" value="{{ $vehicle->vehicle_brand }}" disabled readonly>
              <option value="" selected disabled>Pilih Brand</option>
              @foreach ($brand as $item)
                <option value="{{ $item->id }}" {{ $item->id == $vehicle->vehicle_brand ? 'selected' : '' }}>{{ $item->brand_name }}</option>                      
              @endforeach
            </x-adminlte-select2>
          </div>
          <div class="row">
              <x-adminlte-input name="vehicle_documentno" label="No. STNK" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_documentno" value="{{ $vehicle->vehicle_documentno }}" disabled readonly />

              <x-adminlte-input name="vehicle_color" label="Warna" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_color" value="{{ $vehicle->vehicle_color }}" readonly disabled />
          </div>
          <div class="row">
            <x-adminlte-input name="vehicle_identityno" label="No. Rangka" placeholder=""
            fgroup-class="col-md-6" error-key="vehicle_identityno" value="{{ $vehicle->vehicle_identityno }}" disabled readonly />

            <x-adminlte-input name="vehicle_engineno" label="No. Mesin" placeholder=""
            fgroup-class="col-md-6" error-key="vehicle_engineno" value="{{ $vehicle->vehicle_engineno }}" disabled readonly />
          </div>
          <div class="row">
              <x-adminlte-input name="vehicle_capacity" label="Silinder (cc)" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_capacity" value="{{ $vehicle->vehicle_capacity }}" disabled readonly />

              <x-adminlte-input name="vehicle_last_km" label="Kilometer Terakhir" placeholder=""
              fgroup-class="col-md-6" error-key="vehicle_last_km" value="{{ $vehicle->vehicle_last_km }}" disabled readonly />
          </div>
          <div class="row">
              <x-adminlte-textarea name="vehicle_desc" label="Keterangan" fgroup-class="col-md-12" rows="5" igroup-size="sm" placeholder="Keterangan..." disabled readonly>
                  {{ $vehicle->vehicle_desc }}
              </x-adminlte-textarea>
          </div>            
          <div class="row">
            @php
            $config = [
                'allowedFileTypes' => ['image', 'pdf'],
                'browseOnZoneClick' => false,
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
                // 'uploadUrl' => route('vehicle.file-upload'),
                'uploadAsync' => true,
                // 'deleteUrl' => route('vehicle.file-delete'),
                'uploadExtraData' => ['_token' => csrf_token(), 'match_id' => $vehicle->id],
                'deleteExtraData' => ['_token' => csrf_token(), 'match_id' => $vehicle->id],
                'initialPreviewAsData' => true,
                'initialPreview' => $uploadedFiles,
                'initialPreviewConfig' => $uploadConfigs,
            ];
            @endphp

            <x-adminlte-input-file-krajee name="fileUpload" id="fileUpload" :config="$config" multiple
            accept="application/pdf,image/*" fgroup-class="col-md-12" readonly disabled />
          </div>
        </div>
        @if ($vehicle->insurance)            
          <div class="tab-pane p-3 fade" id="nav-insurance" role="tabpanel" aria-labelledby="nav-insurance-tab">
            <div class="row">
              <x-adminlte-input name="hist_polisno" label="No. Polis" fgroup-class="col-md-6" error-key="hist_polisno" value="{{ $vehicle->insurance->inshist_polishno }}" disabled readonly />
              <x-adminlte-input name="hist_covervalue" label="Nilai Pertanggungan" fgroup-class="col-md-6" class="form-control price" error-key="hist_covervalue" value="{{'Rp. '. number_format($vehicle->insurance->inshist_cover, 0, ',','.') }}" disabled readonly />
            </div>
            <div class="row">
              <x-adminlte-input name="hist_startdate" label="Tgl. Awal Asuransi" fgroup-class="col-md-6" error-key="hist_startdate" value="{{ date('d M Y', strtotime($vehicle->insurance->inshist_startdate)) }}" disabled readonly />
              <x-adminlte-input name="hist_enddate" label="Tgl. Akhir Asuransi" fgroup-class="col-md-6" error-key="hist_enddate" value="{{ date('d M Y', strtotime($vehicle->insurance->inshist_enddate)) }}" disabled readonly />
            </div>
            <div class="row">
              <x-adminlte-input name="hist_premi" label="Premi" fgroup-class="col-md-6" class="form-control" error-key="hist_premi" value="{{'Rp. '. number_format($vehicle->insurance->inshist_premi, 0, ',','.') }}" disabled readonly />
              <x-adminlte-select2 name="hist_vendor" label="Vendor" fgroup-class="col-md-6" error-key="hist_vendor" class="form-control" disabled readonly>
                <option value="" selected disabled>Pilih Vendor</option>
                @foreach ($vendor as $vdr)
                    <option value="{{$vdr->id}}" {{ $vdr->id == $vehicle->insurance->inshist_vendor ? 'selected' : '' }}>{{ $vdr->vdr_name }}</option>
                @endforeach
              </x-adminlte-select2>
            </div>
            <div class="row">
              @php
              $config = [
                  'allowedFileTypes' => ['image', 'pdf'],
                  'browseOnZoneClick' => false,
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
                  // 'uploadUrl' => route('vehicle.file-upload'),
                  'uploadAsync' => true,
                  // 'deleteUrl' => route('vehicle.file-delete'),
                  'uploadExtraData' => ['_token' => csrf_token(), 'match_id' => $vehicle->insurance->id],
                  'deleteExtraData' => ['_token' => csrf_token(), 'match_id' => $vehicle->insurance->id],
                  'initialPreviewAsData' => true,
                  'initialPreview' => $uploadedInsuranceFiles,
                  'initialPreviewConfig' => $uploadInsuranceConfigs,
              ];
              @endphp

              <x-adminlte-input-file-krajee name="fileInsurance[]" id="fileInsurance" :config="$config" multiple
              accept="application/pdf,image/*" fgroup-class="col-md-12" disabled readonly />
            </div>
            <div class="row">
              {{-- history asuransi --}}
              <h5>History Asuransi</h5>
              <table class="table table-bordered responsive" id="ins_history" style="width: 100%;">
                <thead>
                  <th>#</th>
                  <th>No. Polis</th>
                  <th>Tgl. Asuransi</th>
                  <th>Vendor</th>
                  <th>Nilai Pertanggungan</th>
                  <th>Premi</th>
                </thead>
                <tbody>
                  @foreach ($history as $item)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $item->inshist_polishno }}</td>
                      <td>{{ date('d M Y', strtotime($item->inshist_startdate)).' s/d '. date('d M Y', strtotime($item->inshist_enddate))}}</td>
                      <td>{{ $item->vendor->vdr_name }}</td>
                      <td>{{'Rp. '. number_format($item->inshist_cover, 0,',','.') }}</td>
                      <td>{{'Rp. '. number_format($item->inshist_premi, 0,',','.') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @else
          <div class="tab-pane p-3 fade" id="nav-insurance" role="tabpanel" aria-labelledby="nav-insurance-tab">
            <div class="row">
              No. Data Found
            </div>
          </div>
        @endif
      </div>
      <div class="row btn-group">
        <a href="{{route('vehicle.index')}}" class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
      </div>
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
        .flex-end {
          /* background-color: pink; */
          padding: 10px 0;
          display: flex;
          justify-content: flex-end;
        }
    </style>
@stop

@section('js')
<script src="{{ asset('js/krajee/fileinput.min.js') }}"></script>
<!-- sortable.min.js is only needed if you wish to sort / rearrange files in initial preview.
    This must be loaded before fileinput.min.js -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/krajee/sortable.min.js" type="text/javascript"></script>
<!-- piexif.min.js is needed for auto orienting image files OR when restoring exif data in resized images and when you
    wish to resize images before upload. This must be loaded before fileinput.min.js -->
<script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.0/js/krajee/piexif.min.js" type="text/javascript"></script>
  <script>
      $(document).ready(function () {
          if ($('#coverage').val() == 1) {
              console.log('true');
              $('div#insurance').show();

          } else {
              console.log('false');
              $('div#insurance').hide();
          }

          $('#coverage').on('click', function () {
              // alert("OK!");
              if ($(this).is(':checked')) {
                  var checked = $(this).val(true);
              } else {
                  var checked = $(this).val(false);
              }

              var coverage = $(this).val();

              if (coverage == 'true') {
                  console.log('YES');
                  $('div#insurance').show();
              } else {
                  console.log('No.');
                  $('div#insurance').hide();
              }
          });

          $.fn.fileinputBsVersion = "3.3.7"; // if not set, this will be auto-derive

          $("#fileUpload").fileinput({
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

        $("#fileInsurance").fileinput({
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

      });

  </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
