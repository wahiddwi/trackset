@extends('adminlte::page')

@section('title', 'Master Account - Edit')

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
        <form action="{{ route('account.update', $account->coa_account)}}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                <x-adminlte-input name="coa_account" label="Account Number" placeholder=""
                    fgroup-class="col-md-6" error-key="coa_account" type="number" value="{{ $account->coa_account }}" disabled/>

                <x-adminlte-input name="coa_name" label="Account Name" placeholder="" label-class="must-fill"
                    fgroup-class="col-md-6" error-key="coa_name" value="{{ $account->coa_name }}" />
            </div>

            <div class="row">
              <x-adminlte-textarea name="coa_desc" label="Keterangan" fgroup-class="col-md-12" rows="5" igroup-size="sm" placeholder="Masukkan Deskripsi Akun...">{{ $account->coa_desc }}</x-adminlte-textarea>
            </div>
            {{-- <div class="row">
                <x-adminlte-input-switch name="coa_status" label="Status" fgroup-class="col-md-2" data-on-text="YES" data-off-text="NO" value="{{ $value->coa_status }}" />
            </div> --}}

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
