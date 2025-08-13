@extends('adminlte::page')

@section('title', 'Master Location - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Location
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('location.index')}}">Master Location</a></li>
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
        <form action="{{ route('location.store')}}" method="POST">
            @csrf
            <div class="row">
              <x-adminlte-select2 name="site" label="Cabang" fgroup-class="col-md-6" error-key="site" class="form-control">
                <option value="" selected disabled>Pilih Cabang</option>
                    @foreach ($site_list as $site)
                        <option value="{{$site->si_site}}">{{$site->si_site . ' - ' . $site->si_name}}</option>
                    @endforeach
              </x-adminlte-select2>
            </div>

            <div class="row">
              <x-adminlte-input name="loc_id" label="Kode Lokasi" placeholder=""
              fgroup-class="col-md-6" error-key="loc_id"/>
              
                <x-adminlte-input name="name" label="Nama Lokasi" placeholder="" maxlength="60"
                    fgroup-class="col-md-6" error-key="name"/>
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('location.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
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
