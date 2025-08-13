@extends('adminlte::page')

@section('title', 'Master Category Maintenance - Edit')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master
                <small>
                    Category Maintenance
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('cat_maintenance.index')}}">Master Customer</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)

@section('content')
<div class="card col-md-8">
    <div class="card-body">
        <form action="{{ route('cat_maintenance.update', $cat_maintenance->id)}}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
            <x-adminlte-input name="mtn_type" label="Maintenance Type" label-class="must-fill" placeholder="Category Maintenance"
                fgroup-class="col-md-12" error-key="mtn_type" value="{{$cat_maintenance->mtn_type}}"/>
            </div>

            <div class="row">
              <x-adminlte-textarea name="mtn_desc" label="Deskripsi" fgroup-class="col-md-12" rows="3" igroup-size="sm" placeholder="Masukkan Deskripsi Maintenance...">{{ $cat_maintenance->mtn_desc ?  $cat_maintenance->mtn_desc : '' }}</x-adminlte-textarea>
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('cat_maintenance.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

@stop

@section('css')

@stop

@section('js')

@stop
@section('plugins.BootstrapSwitch', true)
