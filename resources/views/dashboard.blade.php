@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    {{-- <p>Welcome to this beautiful admin panel. Test</p> --}}
      <div class="row">
        <div class="col-md-6 col-xl-3">
          <x-adminlte-info-box title="Asset" text="{{ $asset }}" icon="fas fa-lg fa-user-plus text-info"
          theme="info" icon-theme="white"/>
      </div>
      <div class="col-md-6 col-xl-3">
          <x-adminlte-info-box title="Kategori" text="{{ $categories }}" icon="fas fa-lg fa-solid fa-list text-info"
          theme="info" icon-theme="white"/>
      </div>
      <div class="col-md-6 col-xl-3">
        <x-adminlte-info-box title="Tag" text="{{ $tags }}" icon="fas fa-lg fa-solid fa-tags text-info"
        theme="info" icon-theme="white"/>
      </div>
      <div class="col-md-6 col-xl-3">
        <x-adminlte-info-box title="Merk" text="{{ $brand }}" icon="fas fa-lg fa-regular fa-copyright text-info"
        theme="info" icon-theme="white"/>
      </div>
      @if ($totalAssetCategory[0] != null || $totalAssetTag[0] != null || $totalAssetBrand[0] != null)
        <div class="col-md-6 col-xl-6">
          <div class="card">
            <div class="card-body">
              <div id="chartAssetCategory"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-6">
          <div class="card">
            <div class="card-body">
              <div id="chartAssetTag"></div>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-xl-6">
          <div class="card">
            <div class="card-body">
              <div id="chartAssetBrand"></div>
            </div>
          </div>
        </div>
      @else
          
      @endif
    </div>
@stop

@section('plugins.ApexCharts', true)

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
  <script>
    var optionAssetCategory = {
      series: @json($totalAssetCategory),
      title: {
        text: 'Total Asset per Kategori',
        align: 'left',
      },
      chart: {
        height: '100%',
        width: 400,
        type: 'pie',
      },
      labels: @json($categoryName),
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
            height: '100%',
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    };

    var optionAssetTag = {
      series: @json($totalAssetTag),
      title: {
        text: 'Total Asset per Tag',
        align: 'left',
      },
      chart: {
        height: '100%',
        width: 400,
        type: 'pie',
      },
      labels: @json($tagName),
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
            height: '100%',
          },
          legend: {
            position: 'bottom'
          }
        }
      }]
    };

    var optionAssetBrand = {
      series: @json($totalAssetBrand),
      title: {
        text: 'Total Asset per Merk',
        align: 'left',
      },
      chart: {
        height: '100%',
        width: 400,
        type: 'pie',
      },
      labels: @json($brandName),
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
            height: '100%',
          },
          legend: {
            position: 'bottom'
          }
        }
      }],
    };

    var chartAssetCategory = new ApexCharts(document.querySelector("#chartAssetCategory"), optionAssetCategory);
    chartAssetCategory.render();

    var chartAssetTag = new ApexCharts(document.querySelector("#chartAssetTag"), optionAssetTag);
    chartAssetTag.render();

    var chartAssetBrand = new ApexCharts(document.querySelector("#chartAssetBrand"), optionAssetBrand);
    chartAssetBrand.render();
  </script>

@stop
