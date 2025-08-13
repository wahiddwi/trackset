@extends('adminlte::page')

@section('title', 'Master Brand - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master
                <small>
                    Brand
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('brand.index')}}">Master Brand</a></li>
                    <li class="breadcrumb-item active">Create</li>
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
        <form action="{{ route('brand.store')}}" method="POST">
            @csrf
            <div class="row">

            <x-adminlte-input name="brand_name" label="Brand" label-class="must-fill" placeholder="Nama Brand"
                fgroup-class="col-md-10" error-key="brand_name"/>

            <x-adminlte-input-switch name="brand_status" label="Status" label-class="must-fill" fgroup-class="col-md-2" data-on-text="YES" data-off-text="NO" value="1" />
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('brand.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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

        });
    </script>
@stop
@section('plugins.BootstrapSwitch', true)
