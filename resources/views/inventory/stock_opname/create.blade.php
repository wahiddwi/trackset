@extends('adminlte::page')

@section('title', 'Stock Opname - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Stock
                <small>
                    Opname
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('stock_opname.index')}}">Stock Opname</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('stock_opname.store')}}" method="POST">
            @csrf
            <div class="row">
              <x-adminlte-input name="stock_transno" label="No. Transaksi" placeholder="AUTONUMBER" maxlength="60"
              fgroup-class="col-md-6" error-key="stock_transno" disabled/>

              @php
                $config = [
                    'format' => 'DD MMM YYYY',
                    'dayViewHeaderFormat' => 'MMM YYYY',
                ];
              @endphp
              <x-adminlte-input-date name="stock_obtaindate" label="Tgl. Perolehan" igroup-size="md" error-key="inv_obtaindate"
                fgroup-class="col-md-6" :config="$config" value="{{ date('d M Y') }}" enable-old-support disabled>
                <x-slot name="appendSlot">
                  <div class="input-group-text bg-dark">
                    <i class="fas fa-calendar-day"></i>
                  </div>
                </x-slot>
              </x-adminlte-input-date>

              <input type="hidden" name="stock_transdate" value="{{ date('d M Y') }}">
            </div>

            <div class="row">
              <x-adminlte-select2 name="stock_site" id="site" label="Cabang" label-class="must-fill" fgroup-class="col-md-6" error-key="stock_site" class="form-control">
                <option value="" selected disabled>Pilih Cabang</option>
                @foreach ($sites as $site)
                    <option value="{{$site->si_site}}">{{$site->si_site.' - '.$site->si_name }}</option>
                @endforeach
              </x-adminlte-select2>
              
              <x-adminlte-select2 name="stock_loc" id="location" label="Lokasi" label-class="must-fill" fgroup-class="col-md-6" error-key="stock_loc" class="form-control" enable-old-support>
                <option value="" selected disabled>Pilih Lokasi</option>
              </x-adminlte-select2>
            </div>

            <div class="row">
              <x-adminlte-textarea name="stock_desc" label="Keterangan" fgroup-class="col-md-12" rows="5" igroup-size="sm" placeholder="Keterangan..." enable-old-support/>
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" id="btnSubmit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('stock_opname.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')

@stop

@section('js')
  <script>
    $(document).ready(function () {
      $('#site').on('change', function () {
        let siteId = $(this).val();
        $('#location').html('');
        $.ajax({
          type: "POST",
          url: "{{ route('stock.getlocation') }}",
          data: {
            loc_site: siteId,
            _token: "{{ csrf_token() }}"
          },
          dataType: "json",
          success: function (res) {
            // console.log(res);
            $('#location').html('<option value="" selected disabled>Pilih Lokasi</option>');
            $.each(res, function (key, val) { 
               $('#location').append('<option value="'+ val.id +'">'+ val.loc_id +' - '+ val.loc_name +'</option>');
            });
          }
        });
        
      });

      $('#location').on('change', function () {
        let location = $(this).find(':selected').val();
        // console.log(location);
        $.ajax({
          type: "GET",
          url: "{{ route('stock.check-loc') }}",
          data: {
            loc: location,
          },
          success: function (res) {
            // console.log(res.res);
            if (res.res == false) {
              toastr.error('Masih ada SO sebelumnya dilokasi tersebut.', 'Error');
              $('#btnSubmit').attr('disabled', true);
            }
          }
        });
        
      })
    });
  </script>
@stop
@section('plugins.Select2', true)
@section('plugins.TempusDominusBs4', true)

