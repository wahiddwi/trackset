@extends('adminlte::page')

@section('title', 'Calculate Depreciation')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Calculate
                <small>
                    Depreciation
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item">Transacition</li>
                    <li class="breadcrumb-item active"><a href="{{route('depre.index')}}">Calculate Depreciation</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)

@section('content')
<div class="card col-md-6">
    <div class="card-body">
        <form action="{{ route('depre.calculate')}}" method="POST">
            @csrf
            <div class="row">
              <input type="hidden" id="hidden_date" name="cal_dep_transdate">
              <x-adminlte-select2 name="cal_company" label="Perusahaan" fgroup-class="col-md-12" error-key="cal_company"
              class="form-control" placeholder="Pilih Perusahaan" label-class="must-fill">
              <option value="" disabled selected>Pilih Perusahaan</option>
              @foreach ($company as $co)
                  <option value="{{ $co->co_company }}" data-date="{{ $co->last_dep }}"->{{ $co->co_company.' - '.$co->co_name }}</option>
              @endforeach
            </x-adminlte-select2>

            @php
              $config = [
                  'format' => 'MMM-YY',
              ];
            @endphp
          <x-adminlte-input-date name="cal_date" label="Tanggal" igroup-size="md" error-key="cal_date"
            fgroup-class="col-md-12" :config="$config" placeholder="Choose a date..."
            label-class="must-fill">
            <x-slot name="appendSlot">
              <div class="input-group-text bg-dark">
                <i class="fas fa-calendar-day"></i>
              </div>
            </x-slot>
          </x-adminlte-input-date>

            <div class="row btn-group">
                <x-adminlte-button class="btn" id="btn_submit" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('depre.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

@stop

@section('css')

@stop

@section('js')
  <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
  <script>
      $(document).ready(function () {

        // $('#btn_submit').prop('disabled', true);

        $('#cal_company').on('change', function () {
          let url = "{{ route('depre.get-company') }}";
          let selected = $(this).find(':selected').val();
          $.ajax({
            type: "GET",
            url: url,
            data: {
              company: selected
            },
            dataType: "json",
            success: function (res) {
              // console.log(res);
              
              // let last_dep = moment(res.last_dep).format('MMM-YYYY');
              // let nextMonth = moment(last_dep).add(1, 'M').format('MMM YYYY');
              // // $('#hidden_date').val(nextMonth);
              // $('#hidden_date').val(last_dep);
              // // $('#cal_date').val(nextMonth);
              // $('#cal_date').val(last_dep);
              
              // if ($('#cal_date').val() != '') {
              //   $('#btn_submit').prop('disabled', false);
              // } else {
              //   $('#btn_submit').prop('disabled', true);
              // }
            }
          });
          
        })
      });
  </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
@section('plugins.TempusDominusBs4', true)
