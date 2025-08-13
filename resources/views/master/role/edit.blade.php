@extends('adminlte::page')

@section('title', 'Master Roles - Edit')

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
        <form action="{{ route('roles.update', $data->id)}}" method="POST">
            {{ method_field('PATCH') }}
            @csrf

            <div class="row">
                <x-adminlte-input name="name" label="Kode Posisi" placeholder=""
                    fgroup-class="col-md-6" disabled value="{{$data->name}}"/>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <x-adminlte-input name="role_name" label="Nama Posisi" placeholder="" label-class="must-fill"
                    fgroup-class="col-md-6" value="{{$data->role_name}}"/>
                @error('role_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
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
