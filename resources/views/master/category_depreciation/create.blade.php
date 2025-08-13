@extends('adminlte::page')

@section('title', 'Master Category Depreciation - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Category Depreciation
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cat-depreciations.index')}}">Master Category Depreciation</a></li>
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
        <form action="{{ route('cat-depreciations.store')}}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="dep_code" label="Kode" label-class="must-fill" placeholder=""
                fgroup-class="col-md-6" error-key="dep_periode"/>

                <x-adminlte-input name="dep_periode" label="Periode" label-class="must-fill" placeholder="" type="number"
                    fgroup-class="col-md-6" error-key="dep_periode" id="periode"/>
            </div>

            <div class="row">
                <x-adminlte-select name="dep_type" label="Type" label-class="must-fill" fgroup-class="col-md-6" error-key="dep_type" class="form-control" id="type">
                    <option value="" selected disabled>Pilih Tipe Penyusutan</option>
                    <option value="month">Month</option>
                    <option value="year">Year</option>
                </x-adminlte-select>
                <x-adminlte-input name="amount" label="Amount of Depreciations" placeholder="" type="text" id="amount"
                fgroup-class="col-md-6" readonly/>

                <x-adminlte-input name="dep_amount_periode" placeholder="" type="hidden" id="amountHidden"
                fgroup-class="col-md-6" readonly/>
            </div>

            <div class="row">
                <x-adminlte-input-switch name="dep_active" label="Status" fgroup-class="col-md-6" data-on-text="YES" data-off-text="NO" value="1" />
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('cat-depreciations.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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
