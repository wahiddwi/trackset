@extends('adminlte::page')

@section('title', 'Master Category Maintenance - Create')

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
                    <li class="breadcrumb-item"><a href="{{route('cat_maintenance.index')}}">Master Category Maintenance</a></li>
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
        <form action="{{ route('cat_maintenance.store')}}" method="POST">
            @csrf
            <div class="row">
            <x-adminlte-input name="mtn_type" label="Category Maintenance" label-class="must-fill" placeholder="Category Maintenance"
                fgroup-class="col-md-10" error-key="mtn_type"/>

            <x-adminlte-input-switch name="mtn_status" label="Status" label-class="must-fill" fgroup-class="col-md-2" data-on-text="YES" data-off-text="NO" value="1" />
            </div>

            <div class="row">
              <x-adminlte-textarea name="mtn_desc" label="Deskripsi" fgroup-class="col-md-12" rows="3" igroup-size="sm" placeholder="Masukkan Deskripsi Maintenance..."></x-adminlte-textarea>
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
    <script>
        $(document).ready(function () {

        });
    </script>
@stop
@section('plugins.BootstrapSwitch', true)
