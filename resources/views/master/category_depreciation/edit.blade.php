@extends('adminlte::page')

@section('title', 'Master Category Depreciation - Edit')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Category Depreciations
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cat-depreciations.index')}}">Master Location</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('cat-depreciations.update', $data->id)}}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <x-adminlte-input name="dep_code" label="Kode" placeholder="" maxlength="10" value="{{$data->dep_code}}"
                    fgroup-class="col-md-6" disabled/>

                <x-adminlte-input name="dep_periode" id="periode" label="Periode" label-class="must-fill" placeholder="" maxlength="60" value="{{$data->dep_periode}}"
                    fgroup-class="col-md-6" error-key="dep_periode"/>
            </div>
            <div class="row">
                <x-adminlte-select name="dep_type" id="type" label="Type" label-class="must-fill" fgroup-class="col-md-6" error-key="dep_type" class="form-control">
                    @if ($data->dep_type == "month")
                        <option value="{{ $data->dep_type }}" selected>{{ Str::ucfirst($data->dep_type) }}</option>
                        <option value="year">Year</option>
                    @elseif ($data->dep_type == "year")
                        <option value="{{ $data->dep_type }}" selected>{{ Str::ucfirst($data->dep_type) }}</option>
                        <option value="month">Month</option>
                    @else
                        <option value="null" selected>No Depreciation</option>
                    @endif
                </x-adminlte-select>

                <x-adminlte-input name="amount" label="Amount of Depreciations" placeholder="" type="text" id="amount"
                fgroup-class="col-md-6" value="{{$data->dep_amount_periode}}" readonly />

                <x-adminlte-input name="dep_amount_periode" placeholder="" type="hidden" id="amountHidden"
                fgroup-class="col-md-6" value="{{$data->dep_amount_periode}}" readonly/>
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
            $('#amountHidden').val();

            $('#periode').keyup(function () {
                $('#amountHidden').removeAttr('value')
                calculate();
            });

            $('#type').on('change', function () {
                $('#amountHidden').removeAttr('value');
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
