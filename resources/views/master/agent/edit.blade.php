@extends('adminlte::page')

@section('title', 'Master Vendor - Edit')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master
                <small>
                    Vendor
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('agent.index')}}">Master Customer</a></li>
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
        <form action="{{ route('agent.update', $vendor->id)}}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
            <x-adminlte-input name="vendor_code" label="Vendor ID" placeholder="Vendor ID"
                fgroup-class="col-md-4" error-key="vendor_code" value="{{$vendor->vdr_code}}" disabled readonly/>

                <x-adminlte-input name="vdr_name" label="Vendor" label-class="must-fill" placeholder="Nama Vendor"
                fgroup-class="col-md-4" error-key="vdr_name" value="{{$vendor->vdr_name}}"/>

                <x-adminlte-input name="vdr_telp" label="Telp" label-class="must-fill" placeholder="Telp"
                fgroup-class="col-md-4" error-key="vdr_telp" value="{{$vendor->vdr_telp}}" type="number"/>
            </div>

            <div class="row">
              <x-adminlte-textarea name="vdr_addr" label="Alamat" fgroup-class="col-md-6" rows="4" igroup-size="sm" placeholder="Masukkan Alamat Vendor..." label-class="must-fill">{{ $vendor->vdr_addr }}</x-adminlte-textarea>
              <x-adminlte-textarea name="vdr_desc" label="Keterangan" fgroup-class="col-md-6" rows="4" igroup-size="sm" placeholder="Masukkan Keterangan...">{{ $vendor->vdr_desc }}</x-adminlte-textarea>
            </div>



            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('agent.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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
