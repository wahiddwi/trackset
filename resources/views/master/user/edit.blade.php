@extends('adminlte::page')

@section('title', 'Master User - Edit')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Users
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('users.index')}}">Master Users</a></li>
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
        <form action="{{ route('users.update', $data->usr_id)}}" method="POST" id="edit_user">
            {{ method_field('PATCH') }}
            @csrf
            <input type="hidden" name="type" id="type">
            <div class="row">
                <x-adminlte-input name="username" label="NIK" placeholder="" value="{{$data->usr_nik}}" disabled
                    fgroup-class="col-md-6" error-key="username"/>
            </div>
            <div class="row">
                <x-adminlte-input name="name" label="Nama User" label-class="must-fill" placeholder="" value="{{$data->usr_name}}"
                    fgroup-class="col-md-6" error-key="name"/>
                <x-adminlte-select2 name="role_id" label="Posisi" label-class="must-fill" fgroup-class="col-md-6" error-key="role_id" class="form-control">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ $role->id == $data->role_id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-input name="email" label="Email" label-class="must-fill" placeholder="" value="{{$data->usr_email}}"
                    fgroup-class="col-md-6" error-key="email"/>
            </div>

            <div class="row">
              <x-adminlte-input name="password" label="Password" placeholder="" type="password"
                  fgroup-class="col-md-6" error-key="password" />

              <x-adminlte-input name="password_ulang" label="Ulangi Password" placeholder="" type="password"
                  fgroup-class="col-md-6" error-key="password_ulang" />

            </div>

            <input type="hidden" name="role_code" id="role_code">
            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                {{-- <x-adminlte-button class="btn" label="Reset Password" theme="warning" icon="fas fa-lg fa-save" id="btn_reset"/>  --}}
                <a href="{{route('users.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')

@stop

@section('js')
<script>
    $(function(){
        $('#role_code').val($(this).find(":selected").data("role"));

        $('#role_id').on('change', function(){
            $('#role_code').val($(this).find(":selected").data("role"));
        });

        $('#btn_reset').on('click', function(){
            $('#type').val('reset');
            $('#edit_user').submit();
        });
    })
</script>
@stop
@section('plugins.Select2', true)
