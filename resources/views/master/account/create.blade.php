@extends('adminlte::page')

@section('title', 'Master Account - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Account
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('account.index')}}">Master Account</a></li>
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
        <form action="{{ route('account.store')}}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="coa_account" label="Account Number" label-class="must-fill" placeholder=""
                    fgroup-class="col-md-6" error-key="coa_account" type="number"/>

                <x-adminlte-input name="coa_name" label="Account Name" label-class="must-fill" placeholder=""
                    fgroup-class="col-md-6" error-key="coa_name" />
            </div>

            <div class="row">
              <x-adminlte-textarea name="coa_desc" label="Keterangan" fgroup-class="col-md-12" rows="5" igroup-size="sm" placeholder="Masukkan Deskripsi Akun..."/>
            </div>

            <div class="row">
                <x-adminlte-input-switch name="coa_status" label="Status" fgroup-class="col-md-2" data-on-text="YES" data-off-text="NO" data-size="mini" data-base-class="bootstrap-switch" value="1" />
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('account.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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
