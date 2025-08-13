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
        <table class="table table-striped responsive nowrap" id="maintenance_dtl" style="width: 100%;">
          <thead>
            <tr>
              <th colspan="8" class="text-center">Maintenance Detail</th>
            </tr>
            <tr>
              <th>Tanggal</th>
              <th>No. Asset</th>
              <th>Nama Asset</th>
              <th>Keterangan</th>
              <th>Biaya</th>
              <th>Jumlah Service</th>
              <th>&nbsp;</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($maintenance->detail as $item)
              <tr>
                <td>{{ $item->maindtl_transdate }}</td>
                <td>{{ $item->maindtl_asset_transno }}</td>
                <td>{{ $item->maindtl_asset_name }}</td>
                <td>{{ $item->maindtl_desc }}</td>
                <td>{{'Rp. '. number_format($item->maindtl_cost, 0, ',', '.') }}</td>
                <td>{{ $item->maindtl_counter }}</td>
                <td>
                  <a href="{{ route('maintenance.history', ['id' => $item->id]) }}" class="btn btn-success" data-toggle="tooltip" title="HISTORY"><i class="fa-solid fa-clock-rotate-left"></i></a>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="5" class="text-right">Total</th>
              <th class="text-right"><span id="totalCost">Rp. {{ number_format($maintenance->main_total_cost, 0, ',', '.') }}</span></th>
            </tr>
          </tfoot>
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
            $('#maintenance-tabs a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });

            $('#maintenance_history').DataTable({
              responsive: true,
              paging: true,
              ordering: true,
              searching: true,
              order: [[0, 'desc']],
            });

            $('#maintenance_dtl').DataTable({
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
