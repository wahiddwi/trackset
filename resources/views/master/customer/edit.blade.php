@extends('adminlte::page')

@section('title', 'Master Customer - Edit')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master
                <small>
                    Customer
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('customer.index')}}">Master Customer</a></li>
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
        <form action="{{ route('customer.update', $customer->id)}}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
              <x-adminlte-select2 name="cust_type" label="Tipe Identitas" fgroup-class="col-md-6" error-key="cust_type"
              class="form-control" placeholder="Pilih Tipe identitas" label-class="must-fill">
              <option value="KTP" {{$customer->cust_type == 'KTP' ? 'selected' : ''}}>KTP</option>
              <option value="SIM" {{$customer->cust_type == 'SIM' ? 'selected' : ''}}>SIM</option>
              <option value="PASSPORT" {{ $customer->cust_type == 'PASSPORT' ? 'selected' : '' }}>PASSPORT</option>
            </x-adminlte-select2>

            <x-adminlte-input name="cust_no" label="No. Identitas" label-class="must-fill" placeholder="No. Identitas"
                fgroup-class="col-md-6" error-key="cust_no" value="{{$customer->cust_no}}"/>
            </div>

            <div class="row">
              <x-adminlte-input name="cust_name" label="Name" label-class="must-fill" placeholder="Name"
              fgroup-class="col-md-6" error-key="cust_name" value="{{$customer->cust_name}}"/>

              <x-adminlte-input name="cust_telp" label="No. Telp" label-class="must-fill" placeholder="No. Telp"
              fgroup-class="col-md-6" error-key="cust_telp" type="number" value="{{$customer->cust_telp}}"/>
            </div>

            <div class="row">
              <x-adminlte-input name="cust_wa" label="Whatsapp" placeholder="No. Whatsapp"
              fgroup-class="col-md-6" error-key="cust_wa" type="number" value="{{$customer->cust_wa}}"/>

              <x-adminlte-input name="cust_email" label="Email" placeholder="Email"
              fgroup-class="col-md-6" error-key="cust_email" type="email" value="{{$customer->cust_email}}"/>
            </div>
            <div class="row">
                  <div class="form-group mb-3">
                    <input class="form-check-input ml-3" id="cust_internal" type="checkbox" name="cust_internal" value="{{ $customer->cust_internal }}" id="" {{ $customer->cust_internal ? 'checked' : ''}}>
                    {{-- <label class="form-check-label ml-5" for="cust_internal"><b>Internal ?</b><span class="text-danger">*</span></label> --}}
                    <label class="form-check-label ml-5" for="cust_internal"><b>Internal ?</b></label>
                  </div>
            </div>

            <div class="row">
              <x-adminlte-textarea name="cust_addr" fgroup-class="col-md-12" class="form-control" label="Alamat" label-class="must-fill" maxlength="255" rows=5>{{ $customer->cust_addr }}</x-adminlte-textarea>
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('customer.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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
          $('#cus_internal').bootstrapSwitch('state', {{$customer->cust_internal}});
        });
    </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
