@extends('adminlte::page')

@section('title', 'Master Vendor - Create')

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
                    <li class="breadcrumb-item"><a href="{{route('agent.index')}}">Master Vendor</a></li>
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
        <form action="{{ route('agent.store')}}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="vdr_code" label="No. Vendor" placeholder="" label-class="must-fill"
                fgroup-class="col-md-6" error-key="vdr_code" />

                <x-adminlte-input name="vdr_name" label="Nama Vendor" label-class="must-fill" placeholder="Nama Vendor"
                fgroup-class="col-md-6" error-key="vdr_name" />
            </div>

            <div class="row">
                <x-adminlte-input name="vdr_telp" label="No. Telp" label-class="must-fill" placeholder="No. Telp"
                fgroup-class="col-md-6" error-key="vdr_telp" type="number" />

                <x-adminlte-input-switch name="vdr_status" label="Status" label-class="must-fill" fgroup-class="col-md-2" data-on-text="YES" data-off-text="NO" value="1" />
            </div>

            <div class="row">
              <x-adminlte-textarea name="vdr_addr" label="Alamat" fgroup-class="col-md-6" rows="4" igroup-size="sm" placeholder="Masukkan Alamat Vendor..." label-class="must-fill"/>
              <x-adminlte-textarea name="vdr_desc" label="Keterangan" fgroup-class="col-md-6" rows="4" igroup-size="sm" placeholder="" />
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
    <script>
        $(document).ready(function () {

        });
    </script>
@stop
@section('plugins.BootstrapSwitch', true)
