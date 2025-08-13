@extends('adminlte::page')

@section('title', 'Master Location - Edit')

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
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('location.update', $data->id)}}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="row">
              <x-adminlte-select2 name="site" label="Cabang" fgroup-class="col-md-6" error-key="site" class="form-control">
                <option value="" selected disabled>Pilih Cabang</option>
                @foreach ($site_list as $site)
                    @if ($site->si_site == $data->loc_site)
                        <option value="{{$site->si_site}}" selected>{{$site->si_site . ' - ' . $site->si_name}}</option>
                    @else
                        <option value="{{$site->si_site}}">{{$site->si_site . ' - ' . $site->si_name}}</option>
                    @endif
                @endforeach
            </x-adminlte-select2>
            </div>

            <div class="row">
              <x-adminlte-input name="loc_id" label="Kode" placeholder="" maxlength="10" value="{{$data->loc_id}}"
                fgroup-class="col-md-6" error-key="loc_id" disabled/>
                
                <x-adminlte-input name="name" label="Nama Lokasi" placeholder="" maxlength="60" value="{{$data->loc_name}}"
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
