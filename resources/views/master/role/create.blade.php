@extends('adminlte::page')

@section('title', 'Master Roles - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Roles
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('roles.index')}}">Master Roles</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('roles.store')}}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="name" label="Kode Posisi" label-class="must-fill" placeholder=""
                    fgroup-class="col-md-6" error-key="name"/>

                <x-adminlte-input name="role_name" label="Nama Posisi" placeholder="" label-class="must-fill"
                    fgroup-class="col-md-6" error-key="role_name"/>
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('roles.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')

@stop

@section('js')

@stop
