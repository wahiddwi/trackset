@extends('layouts.print_layout')

@section('title', $data->trf_transno)
    
@section('footer_transno', $data->trf_transno)

@section('content')
    
  <table style="width:100%">
    <tr>
      <td class="text-left" colspan="3">
        <span class="title">{{ $data->trf_transno }}</span>
      </td>
      <td class="text-right" style="width: 40%">
        <span class="sub-title">TRANSFER</span>
        <span class="site-title">{{ strtoupper($data->company->co_name) }}</span>
      </td>
    </tr>
    <tr>
      <td class="text-left" style="width: 100px">Cabang</td>
      <td class="text-left" style="width: 10px">:</td>
      <td class="text-left">{{ $data->trf_site_from . ' - ' . $data->siteFrom->si_name }}</td>
      <td class="text-right">{{ $data->trf_transdate->format('d M Y') }}</td>
    </tr>
    <tr>
      <td class="text-left align-top">Gudang</td>
      <td class="text-left align-top">:</td>
      <td class="text-left align-top">{{ $data->locFrom->loc_name . ' (' . $data->locFrom->loc_id . ')' }}</td>
    </tr>
    <tr style="height: 10px">
      <td class="text-left align-top">Keterangan</td>
      <td class="text-left align-top">:</td>
      <td class="text-left align-top" colspan="2">{{ $data->trf_desc ? $data->trf_desc : '' }}</td>
    </tr>
  </table>

  <br>

  <table class="table border" style="border-collapse: collapse; width: 100%;">
    <thead>
      <tr>
        <th class="text-center" style="width: 4%;">#</th>
        <th style="width: 18%;">Cabang</th>
        <th style="width: 20%;">Lokasi</th>
        <th>Asset</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data->detail as $dtl)
        <tr>
          <td class="text-center">{{ $loop->iteration }}</td>
          <td class="text-left">{{ $data->siteTo->si_site . ' - ' . $data->siteTo->si_name }}</td>
          <td class="text-left">{{ $data->locTo->loc_name . ' (' . $data->locTo->loc_id .')' }}</td>
          <td class="text-left">{{ $dtl->trfdtl_name }}</td>
          <td class="text">{{ $dtl->trfdtl_desc }}</td>
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
        <td class="pl-2">{{ $data->trf_created_name }}</td>
      </tr>
      <tr>
        <td>Disetujui Oleh</td>
        <td class="pl-2">:</td>
        <td class="pl-2">{{ $data->trf_approver_name }}</td>
      </tr>
    </table>
    <br>
    <small>* INI ADALAH CETAKAN KOMPUTER, TANDATANGAN TIDAK DIPERLUKAN</small><br>
    <small>Dicetak pada: {{ date('d M Y H:i:s') }} - {{ Auth::user()->usr_name }}</small>
  </div>
    
@endsection
