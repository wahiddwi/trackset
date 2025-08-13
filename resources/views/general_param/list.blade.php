@extends('adminlte::page')
@section('title', 'General Parameter')
@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    General Parameter
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">General Parameter</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop
@section('content')
<div class="col-md-8">
  <div class="card">
    <div class="card-body">
      <form action="{{ route('params.update', $param->id) }}" method="POST" id="formParams">
        @csrf
        @method('PATCH')
        <x-adminlte-select2 name="param_sales_profit" label="Laba Penjualan Asset Tetap" label-class="must-fill" fgroup-class="col-md-12" error-key="param_sales_profit" class="form-control">
          <option value="" selected disabled>Pilih Akun Laba Penjualan</option>
          @foreach ($account as $acc)
              <option value="{{ $acc->coa_account }}" {{ $param->param_sales_profit == $acc->coa_account ? 'selected' : '' }}>{{ $acc->coa_account.' - '.$acc->coa_name }}</option>
          @endforeach
        </x-adminlte-select2>
        <x-adminlte-select2 name="param_sales_loss" label="Rugi Penjualan Asset Tetap" label-class="must-fill" fgroup-class="col-md-12" error-key="param_sales_loss" class="form-control">
          <option value="" selected disabled>Pilih Akun Rugi Penjualan</option>
          @foreach ($account as $acc)
              <option value="{{ $acc->coa_account }}" {{ $param->param_sales_loss == $acc->coa_account ? 'selected' : '' }}>{{ $acc->coa_account.' - '.$acc->coa_name }}</option>
          @endforeach
        </x-adminlte-select2>
        <x-adminlte-select2 name="param_expense_loss" label="Rugi Beban Write Off" label-class="must-fill" fgroup-class="col-md-12" error-key="param_expense_loss" class="form-control">
          <option value="" selected disabled>Pilih Akun Rugi Beban</option>
          @foreach ($account as $acc)
            <option value="{{ $acc->coa_account }}" {{ $param->param_expense_loss == $acc->coa_account ? 'selected' : '' }}>{{ $acc->coa_account.' - '.$acc->coa_name }}</option>
          @endforeach
        </x-adminlte-select2>
        <x-adminlte-select2 name="param_asset_transaction" label="Transaksi Aktiva Tetap" label-class="must-fill" fgroup-class="col-md-12" error-key="param_asset_transaction" class="form-control">
          <option value="" selected disabled>Pilih Akun Transaksi Aktiva Tetap</option>
          @foreach ($account as $acc)
            <option value="{{ $acc->coa_account }}" {{ $param->param_asset_transaction == $acc->coa_account ? 'selected' : '' }}>{{ $acc->coa_account.' - '.$acc->coa_name }}</option>
          @endforeach
        </x-adminlte-select2>
        <x-adminlte-select2 name="param_cash" label="Kas" label-class="must-fill" fgroup-class="col-md-12" error-key="param_cash" class="form-control">
          <option value="" selected disabled>Pilih Akun Kas</option>
          @foreach ($account as $acc)
            <option value="{{ $acc->coa_account }}" {{ $param->param_cash == $acc->coa_account ? 'selected' : '' }}>{{ $acc->coa_account.' - '.$acc->coa_name }}</option>
          @endforeach
        </x-adminlte-select2>
      </form>
    </div>
  </div>
</div>
@stop
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('css')
@stop
@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script>
      $(document).ready(function () {
        $('#param_sales_profit').on('change', function () {
          $(this).find(':selected').val();
          $('#formParams').submit();          
        });
        $('#param_sales_loss').on('change', function () {
          $(this).find(':selected').val();
          $('#formParams').submit();          
        });
        $('#param_expense_loss').on('change', function () {
          $(this).find(':selected').val();
          $('#formParams').submit();          
        });
        $('#param_asset_transaction').on('change', function () {
          $(this).find(':selected').val();
          $('#formParams').submit();          
        });
        $('#param_cash').on('change', function () {
          $(this).find(':selected').val();
          $('#formParams').submit();          
        });
      });
    </script>
@stop
