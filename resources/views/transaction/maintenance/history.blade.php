@extends('adminlte::page')

@section('title', 'Maintenance - Detail')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Detail & History Maintenance
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('maintenance.index') }}">Maintenance</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
      <div class="card-body">
        <table class="table table-bordered table-striped responsive nowrap" id="maintenance_history">
          <thead>
            <tr>
              <th colspan="7" class="text-center">Maintenance History</th>
            </tr>
            <tr>
              <th>Tanggal</th>
              <th>No. Asset</th>
              <th>Asset</th>
              <th>Keterangan</th>
              <th>Biaya</th>
              <th>Jumlah Service</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($history as $item)
              <tr>
                <td>{{ $item->mainhist_transdate }}</td>
                <td>{{ $item->mainhist_asset_transno }}</td>
                <td>{{ $item->mainhist_asset_name }}</td>
                <td>{{ $item->mainhist_desc }}</td>
                <td>{{'Rp. '. number_format($item->mainhist_cost, 0, ',', '.') }}</td>
                <td>{{ $item->mainhist_count }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
          <div class="row btn-group">
            <a href="{{ route('maintenance.index') }}" class="btn btn-primary"><i class="fas fa-lg fa-arrow-left"></i>
                Back</a>
        </div>
      </div>
    </div>
@stop

@section('css')
    <style>
        table td .form-group {
            margin-bottom: 0px !important;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>
    <script>
      $(document).ready(function() {
        $('#maintenance_history').DataTable({
          responsive: true,
          paging: true,
          ordering: true,
          searching: true,
        });
      });
    </script>
@stop

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.KrajeeFileinput', true)
