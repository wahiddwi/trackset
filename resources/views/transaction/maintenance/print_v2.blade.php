@extends('layouts.print_layout')

@section('title', $data->main_transno)
    
@section('footer_transno', $data->main_transo)

@section('content')
    
  <table style="width:100%">
    <tr>
      <td class="text-left" colspan="3">
        <span class="title">{{ $data->main_transno }}</span>
      </td>
      <td class="text-right" style="width: 40%">
        <span class="sub-title">MAINTENANCE ASSET</span>
        <span class="site-title">{{ strtoupper($data->company->co_name) }}</span>
      </td>
    </tr>
    <tr>
      <td class="text-left" style="width: 100px">Vendor</td>
      <td class="text-left" style="width: 10px">:</td>
      <td class="text-left">{{ $data->vendor->vdr_name }}</td>
      <td class="text-right">{{ $data->main_transdate->format('d M Y') }}</td>
    </tr>
    <tr style="height: 10px;">
      <td class="text-left align-top">No. Telp</td>
      <td class="text-left align-top">:</td>
      <td class="text-left align-top">{{ $data->vendor->vdr_telp }}</td>
    </tr>
  </table>

  <br>

  <table class="table border" style="border-collapse: collapse; width: 100%;">
    <thead>
      <tr>
        <th class="text-center" style="width: 4%;">#</th>
        <th>No. Asset</th>
        <th>Asset</th>
        <th>Keterangan</th>
        <th>Service</th>
        <th>Biaya</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data->detail as $dtl)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td class="text-left">{{ $dtl->maindtl_asset_transno }}</td>
          <td class="text-left">{{ $dtl->maindtl_asset_name }}</td>
          <td class="text-left">{{ $dtl->maindtl_desc ? $dtl->maindtl_desc : '' }}</td>
          <td class="text-center">{{ $dtl->maindtl_counter }}</td>
          <td class="text-right">{{ 'Rp. ' . number_format($dtl->maindtl_cost) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <br>
  {{-- Footer TTD --}}
  <div class="footer" style="width:100%">
    <hr>
    <table style="width:30%">
      <tr>
        <td>Dibuat Oleh</td>
        <td class="pl-2">:</td>
        <td class="pl-2">{{ $data->created_by_name }}</td>
      </tr>
      <tr>
        <td>Disetujui Oleh</td>
        <td class="pl-2">:</td>
        <td class="pl-2">{{ $data->approver_by_name }}</td>
      </tr>
    </table>
    <br>
    <small>* INI ADALAH CETAKAN KOMPUTER, TANDATANGAN TIDAK DIPERLUKAN</small><br>
    <small>Dicetak pada: {{ date('d M Y H:i:s') }} - {{ Auth::user()->usr_name }}</small>
  </div>
    
@endsection
