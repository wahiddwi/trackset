@extends('adminlte::page')

@section('title', 'Master Tag - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master
                <small>
                    Tag
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('tag.index')}}">Master Tag</a></li>
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
        <form action="{{ route('tag.store')}}" method="POST">
            @csrf
            <div class="row">

            <x-adminlte-input name="tag_name" label="Tag" label-class="must-fill" placeholder="Nama Tag"
                fgroup-class="col-md-10" error-key="tag_name"/>

            <x-adminlte-input-switch name="tag_status" label="Status" label-class="must-fill" fgroup-class="col-md-2" data-on-text="YES" data-off-text="NO" value="1" />
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('tag.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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
