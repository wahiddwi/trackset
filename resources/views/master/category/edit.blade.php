@extends('adminlte::page')

@section('title', 'Master Category - Edit')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Categories
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Master Categories</a></li>
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
        <form action="{{ route('categories.update', $data->id)}}" method="POST" id="edit_category">
            @csrf
            @method('PATCH')
            <div class="row">
                <x-adminlte-input name="cat_code" label="ID Kategori" placeholder="" value="{{ $data->cat_code }}"
                fgroup-class="col-md-6" error-key="code" readonly />

                <x-adminlte-input name="cat_name" label="Nama Kategori" label-class="must-fill" placeholder=""
                fgroup-class="col-md-6" error-key="name" value="{{ $data->cat_name }}" />
            </div>

            <div class="row">

                {{-- <x-adminlte-input name="cat_percent" id="cat_percent" label="Percent Of Depreciation" placeholder="" type="number"
                fgroup-class="col-md-6" error-key="cat_percent" value="{{ $data->cat_percent }}">
                    <x-slot name="appendSlot">
                        <div class="input-group-text">
                            <i class="fa fa-percent"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input> --}}

                <x-adminlte-select2 name="cat_asset" label="Akun Asset" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_account" class="form-control">
                    @foreach ($account as $acc)
                        <option value="{{$acc->coa_account}}" {{ $acc->coa_account == $data->cat_asset ? "selected" : "" }}>{{ $acc->coa_name }}</option>
                    @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="cat_depreciation" label="Penyusutan" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_depreciation" class="form-control">
                    @foreach ($depreciation as $dep)
                        @if ($dep->dep_amount_periode == null)
                            <option value="{{$dep->id}}" {{ $dep->id == $data->cat_depreciation ? 'selected' : '' }}>No Depreciation</option>
                        @else
                            <option value="{{$dep->id}}" {{ $dep->id == $data->cat_depreciation ? 'selected' : '' }}>{{ $dep->dep_amount_periode.' ' }}{{  $dep->dep_amount_periode > 1 ? 'Months' : 'Month' }}</option>
                        @endif
                    @endforeach
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-select2 name="cat_accumulate_depreciation" label="Akun Akumulasi Penyusutan" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_accumulate_depreciation" class="form-control">
                    @foreach ($account as $acc)
                    <option value="{{$acc->coa_account}}" {{ $acc->coa_account == $data->cat_accumulate_depreciation ? "selected" : "" }}>{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                        @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="cat_depreciation_expense" label="Akun Beban Penyusutan" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_depreciation_expense" class="form-control">
                    @foreach ($account as $acc)
                    <option value="{{$acc->coa_account}}" {{ $acc->coa_account == $data->cat_depreciation_expense ? "selected" : "" }}>{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                        @endforeach
                </x-adminlte-select2>
            </div>

            {{-- <div class="row">
                <x-adminlte-select2 name="cat_income" label="Akun Pendapatan" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_income" class="form-control">
                    @foreach ($account as $acc)
                        <option value="{{$acc->coa_account}}" {{ $acc->coa_account == $data->cat_income ? "selected" : "" }}>{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                    @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="cat_disposal" label="Akun Disposal" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_disposal" class="form-control">
                    @foreach ($account as $acc)
                        <option value="{{$acc->coa_account}}" {{ $acc->coa_account == $data->cat_disposal ? "selected" : "" }}>{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                    @endforeach
                </x-adminlte-select2>
            </div> --}}

            {{-- <div class="row">
                <x-adminlte-input-switch name="cat_active" label="Status" fgroup-class="col-md-6" data-on-text="YES" data-off-text="NO" checked/>
            </div> --}}

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('categories.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')

@stop

@section('js')
<script>
    $(function(){
        $('#role_code').val($(this).find(":selected").data("role"));

        $('#role_id').on('change', function(){
            $('#role_code').val($(this).find(":selected").data("role"));
        });

        $('#btn_reset').on('click', function(){
            $('#type').val('reset');
            $('#edit_user').submit();
        });


    })
</script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)

