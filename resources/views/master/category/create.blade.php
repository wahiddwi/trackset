@extends('adminlte::page')

@section('title', 'Master Category - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Category
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Master Category</a></li>
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
        <form action="{{ route('categories.store')}}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="cat_code" label="ID Kategori" label-class="must-fill" placeholder=""
                fgroup-class="col-md-6" error-key="cat_code"/>

                <x-adminlte-input name="cat_name" label="Nama Kategori" label-class="must-fill" placeholder=""
                    fgroup-class="col-md-6" error-key="name"/>

                {{-- <x-adminlte-input name="cat_percent" id="cat_percent" label="Percent Of Depreciation" placeholder="" type="number"
                fgroup-class="col-md-4" error-key="cat_percent">
                    <x-slot name="appendSlot">
                        <div class="input-group-text">
                            <i class="fa fa-percent"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input> --}}

            </div>

            <div class="row">
                <x-adminlte-select2 name="cat_asset" label="Akun Asset" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_asset" class="form-control">
                    <option value="" selected disabled>Pilih Akun Asset </option>
                    @foreach ($account as $acc)
                    <option value="{{$acc->coa_account}}">{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                        @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="cat_depreciation" label="Penyusutan" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_depreciation" class="form-control">
                    <option value="" selected disabled>Pilih Depresiasi</option>
                        @foreach ($depreciation as $dep)
                            @if ($dep->dep_amount_periode == null)
                                <option value="{{$dep->id}}">No Depreciation</option>
                            @else
                                <option value="{{$dep->id}}">{{ $dep->dep_amount_periode.' ' }}{{  $dep->dep_amount_periode > 1 ? 'Months' : 'Month' }}</option>
                            @endif
                        @endforeach
                </x-adminlte-select2>

            </div>

            <div class="row">
                <x-adminlte-select2 name="cat_accumulate_depreciation" label="Akun Akumulasi Penyusutan" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_accumulate_depreciation" class="form-control">
                    <option value="" selected disabled>Pilih Akun Akumulasi Penyusutan</option>
                    @foreach ($account as $acc)
                    <option value="{{$acc->coa_account}}">{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                        @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="cat_depreciation_expense" label="Akun Beban Penyusutan" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_depreciation_expense" class="form-control">
                    <option value="" selected disabled>Pilih Akun Beban Penyusutan</option>
                    @foreach ($account as $acc)
                    <option value="{{$acc->coa_account}}">{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                        @endforeach
                </x-adminlte-select2>
            </div>

            {{-- <div class="row">
                <x-adminlte-select2 name="cat_income" label="Akun Pendapatan" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_income" class="form-control">
                    <option value="" selected disabled>Pilih Akun Pendapatan</option>
                    @foreach ($account as $acc)
                    <option value="{{$acc->coa_account}}">{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                        @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="cat_disposal" label="Akun Disposal" label-class="must-fill" fgroup-class="col-md-6" error-key="cat_disposal" class="form-control">
                    <option value="" selected disabled>Pilih Akun Disposal</option>
                    @foreach ($account as $acc)
                    <option value="{{$acc->coa_account}}">{{ $acc->coa_account }} - {{ $acc->coa_name }}</option>
                        @endforeach
                </x-adminlte-select2>
            </div> --}}

            <div class="row">
                <x-adminlte-input-switch name="cat_active" label="Status" fgroup-class="col-md-6" data-on-text="YES" data-off-text="NO" checked/>
                <x-adminlte-input-switch name="is_vehicle" label="Apakah ini termasuk tipe kendaraan?" fgroup-class="col-md-6" data-on-text="YES" data-off-text="NO"/>
            </div>

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

@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
