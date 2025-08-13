@extends('adminlte::page')

@section('title', 'Master Customer - Create')

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
                    <li class="breadcrumb-item active">Create</li>
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
        <form action="{{ route('customer.store')}}" method="POST">
            @csrf
            <div class="row">
              <x-adminlte-select2 name="cust_type" label="Tipe Identitas" fgroup-class="col-md-6" error-key="cust_type"
              class="form-control" placeholder="Pilih Tipe identitas" label-class="must-fill">
              <option value="" disabled selected>Pilih Tipe Identitas</option>
              <option value="KTP">KTP</option>
              <option value="SIM">SIM</option>
              <option value="PASSPORT">PASSPORT</option>
            </x-adminlte-select2>

            <x-adminlte-input name="cust_no" label="No. Identitas" label-class="must-fill" placeholder="No. Identitas"
                fgroup-class="col-md-6" error-key="cust_no"/>
            </div>

            <div class="row">
              <x-adminlte-input name="cust_name" label="Name" label-class="must-fill" placeholder="Name"
              fgroup-class="col-md-6" error-key="cust_name"/>

              <x-adminlte-input name="cust_telp" label="No. Telp" label-class="must-fill" placeholder="No. Telp"
              fgroup-class="col-md-6" error-key="cust_telp" type="number"/>
            </div>

            <div class="row">
              <x-adminlte-input name="cust_wa" label="Whatsapp" placeholder="No. Whatsapp"
              fgroup-class="col-md-6" error-key="cust_wa" type="number"/>

              <x-adminlte-input name="cust_email" label="Email" placeholder="Email"
              fgroup-class="col-md-6" error-key="cust_email" type="email"/>
            </div>

            <div class="row">
              <x-adminlte-input-switch name="cust_active" label="Customer Status" fgroup-class="col-md-6" data-on-text="ACTIVE" data-off-text="INACTIVE" value="1" />
              <x-adminlte-input-switch name="cust_internal" label="Internal ?" fgroup-class="col-md-6" data-on-text="YES" data-off-text="NO" value="1" />
            </div>

            <div class="row">
              <x-adminlte-textarea name="cust_addr" fgroup-class="col-md-12" class="form-control" label="Alamat" label-class="must-fill" maxlength="255" rows=5 />
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
            $('#periode').keyup(function () {
                calculate();
            });

            $('#type').on('change', function () {
                calculate();
            });
        });

        function calculate() {
            var periode = $('#periode').val();
            var type = $('#type option:selected').val();

            if (type == 'year') {
                var value = periode*12
                var result = value + ' Months'
            } else {
                var value = periode
                var result = value <= 1 ? value+' Month': value + ' Months'

            }
            $('#amount').val(result)
            $('#amountHidden').val(value)
        }
    </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
