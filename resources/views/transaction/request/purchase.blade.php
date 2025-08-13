@extends('adminlte::page')

@section('title', 'Pengajuan Pembelian Asset')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Pengajuan Pembelian Asset
                        <small>
                            {{-- <span class="badge badge-primary">{{ $count }}</span> --}}
                        </small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Pengajuan Pembelian Asset</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<form action="{{ route('asset_request.purchase_process') }}" method="POST">
  <div class="card">
    <div class="card-body">
        <div class="row">
          <x-adminlte-input name="req_spb" label="SPB No." disabled fgroup-class="col-md-6" value="{{ $req->req_spb }}" />
            @php
              $config = [
                  'format' => 'DD MMM YYYY',
                  'dayViewHeaderFormat' => 'MMM YYYY',
              ];
            @endphp
            <x-adminlte-input-date name="main_transdate" id="main_transdate" label="Tgl. Transaksi" igroup-size="md" error-key="main_transdate"
              fgroup-class="col-md-6" :config="$config" value="{{ $req->req_transdate }}" label-class="must-fill" enable-old-support disabled>
              <x-slot name="appendSlot">
                <div class="input-group-text bg-dark">
                  <i class="fas fa-calendar-day"></i>
                </div>
              </x-slot>
            </x-adminlte-input-date>
        </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered responsive nowrap" id="purchase_table">
          <thead>
            <tr>
              <th class="text-center">No</th>
              <th class="text-center">Item</th>
              <th class="text-center">Permintaan</th>
              <th class="text-center">Transfer</th>
              <th class="text-center">Beli</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($req->detail as $item)
            @php
                $qtyPurchase = ($item->reqdtl_qty_approve - $item->reqdtl_qty_send);
            @endphp
              <tr>
                <td>
                  {{ $loop->iteration }}
                  <input type="hidden" name="trfdtl_order[]" value="{{ $item->reqdtl_line }}">
                  <input type="hidden" name="trfdtl_id[]" value="{{ $item->reqdtl_id }}">
                  <input type="hidden" name="trfdtl_item[]" value="{{ $item->reqdtl_item }}">
                  <input type="hidden" name="trfdtl_code[]" value="{{ $item->reqdtl_code }}">
                  <input type="hidden" name="trfdtl_uom[]" value="{{ $item->reqdtl_uom }}">
                  <input type="hidden" name="trfdtl_qty[]" value="{{ $item->reqdtl_qty }}">
                  <input type="hidden" name="trfdtl_qty_approve[]" value="{{ $item->reqdtl_qty_approve }}">
                  <input type="hidden" name="trfdtl_qty_send[]" value="{{ $item->reqdtl_qty_send }}">
                  <input type="hidden" name="trfdtl_qty_purchase[]" value="{{ $qtyPurchase }}">
                </td>
                <td>{{ $item->reqdtl_item }}</td>
                <td>{{ $item->reqdtl_qty_approve }}</td>
                <td>{{ $item->reqdtl_qty_send }}</td>
                <td>{{ $qtyPurchase }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="row btn-group">
        <a href="{{ route('asset_request.index') }}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i>
            Cancel</a>
          <x-adminlte-button class="btn" label="Beli" theme="success" icon="fas fa-lg fa-solid fa-store"
              id="btn_buy" type="submit" />
      </div>
    </div>
    <div class="overlay" id="load-overlay" hidden>
      <i class="fas fa-2x fa-sync-alt fa-spin"></i>
    </div>
  </div>
</form>
@stop
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)
@section('css')
    <style>
        .btn-group {
            text-align: right;
            float: right;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script>
      function setDatatable() {
        let tb = $('#purchase_table').dataTable();
      }

      $(function () {
        setDatatable();
      })
    </script>
@stop
