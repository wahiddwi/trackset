@extends('adminlte::page')

@section('title', 'Master Modules - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Modules
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('modules.index')}}">Master Modules</a></li>
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
        <form action="{{ route('modules.store')}}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="code" label="Module Code" placeholder=""
                    fgroup-class="col-md-6" error-key="code"/>
                    
                <x-adminlte-input name="name" label="Name" placeholder=""
                    fgroup-class="col-md-6" error-key="name"/>
            </div>
            <div class="row">
                <x-adminlte-input name="desc" label="Description" placeholder=""
                    fgroup-class="col-md-6" error-key="desc"/>

                <x-adminlte-input name="module" label="Module" placeholder=""
                    fgroup-class="col-md-6" error-key="module"/>
            </div>
            <div class="row">
                <x-adminlte-input name="icon" label="Icon" placeholder=""
                    fgroup-class="col-md-6" error-key="icon"/>

                <x-adminlte-input-switch name="isSuperuser" label="Superuser" fgroup-class="col-md-2" data-on-text="YES" data-off-text="NO" value="1" />
                    
            </div>
            <div class="row">
                <x-adminlte-select2 name="parent" label="Parent Menu" fgroup-class="col-md-6" error-key="parent" class="form-control">
                    <option value="">No Parent</option>
                    @foreach ($modules as $module)
                        <option value="{{$module->mod_id}}">{{$module->mod_code . ' - ' . $module->mod_name}}</option>
                    @endforeach
                </x-adminlte-select2>

                <x-adminlte-input name="order" label="Order" placeholder="" type="number"
                    fgroup-class="col-md-6" error-key="order"/>
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/> 
                <a href="{{route('modules.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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