@extends('adminlte::page')

@section('title', 'Master PIC - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master
                <small>
                    PIC
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('pic.index')}}">Master PIC</a></li>
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
        <form action="{{ route('pic.store')}}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="pic_nik" label="NIK" label-class="must-fill" placeholder=""
                    fgroup-class="col-md-6" error-key="pic_nik" type="number"/>

                <x-adminlte-input name="pic_name" label="Name" label-class="must-fill" placeholder=""
                    fgroup-class="col-md-6" error-key="pic_name" />
            </div>

            <div class="row">
                <x-adminlte-input-switch name="pic_status" label="Status" fgroup-class="col-md-2" data-on-text="YES" data-off-text="NO" data-size="mini" data-base-class="bootstrap-switch" value="1" />
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('pic.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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
