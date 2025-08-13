@extends('adminlte::page')

@section('title', 'Journal Asset')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Journal
                    <small>
                        Asset
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Journal Asset</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div id="alert">

        </div>

        <div class="row">
            <x-adminlte-select2 name="company" igroup-size="md" label="Perusahaan" label-class="must-fill" id="company" fgroup-class="col-md-4" error-key="company"
                data-placeholder="Select an option...">
                <x-slot name="prependSlot">
                    <div class="input-group-text text-primary">
                        <i class="fa-regular fa-building"></i>
                    </div>
                </x-slot>
                <option value="" selected disabled>Pilih Perusahaan</option>
                @foreach ($company as $comp)
                    <option value="{{ $comp->si_company }}">{{ $comp->company->co_company }} - {{ $comp->company->co_name }}</option>
                @endforeach
            </x-adminlte-select2>

            <x-adminlte-select2 name="site" igroup-size="md" label="Cabang" label-class="must-fill" id="site" fgroup-class="col-md-4" error-key="site"
              data-placeholder="Pilih Cabang">
              <option value="" selected disabled>Pilih Cabang</option>
                <x-slot name="prependSlot">
                    <div class="input-group-text text-primary">
                        <i class="fa-regular fa-building"></i>
                    </div>
                </x-slot>
            </x-adminlte-select2>

            @php
                $config = ['format' => 'MMM YY'];
            @endphp
            <x-adminlte-input-date name="periode" :config="$config" id="periode" label="Periode" label-class="must-fill" igroup-size="md"
                fgroup-class="col-md-4" error-key="periode" placeholder="Choose a periode..." value="{{ Carbon\Carbon::now()->format('M y') }}">
                <x-slot name="prependSlot">
                    <div class="input-group-text text-primary">
                        <i class="fa-regular fa-calendar"></i>
                    </div>
                </x-slot>
            </x-adminlte-input-date>
        </div>
        {{-- <div class="row">
            <x-adminlte-input name="search" label="Search" placeholder="search...." id="search" igroup-size="md" fgroup-class="col-md-6" error-key="search">
                <x-slot name="prependSlot">
                    <div class="input-group-text text-primary">
                        <i class="fas fa-search"></i>
                    </div>
                </x-slot>
            </x-adminlte-input>
        </div> --}}

        <div class="row btn-group">
            <a href="javascript:" type="search" id="btnSearch" class="btn btn-success"><i class="fa-brands fa-searchengin"></i> Cari.</a>
        </div>
    </div>
</div>

@stop
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)
@section('plugins.Select2', true)

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
    <style>
        .btn-group {
            margin-left: 2px;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).ready( function () {

            function search(){
                let company = $('#company').val();
                let periode = $('#periode').val();
                let site = $('#site').val();

                $.ajax({
                    type: "GET",
                    url: "{{ url('journal-asset/search') }}",
                    data: {
                        company: company,
                        periode: periode,
                        site: site,
                    },
                    dataType: "json",
                    success: function (res) {
                        // console.log(res);
                        if (res.res == false) {
                            $('#alert').html(`
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                        <span id="alertMsg">${res.message}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `);
                        } else {
                            $('#alert').html(`
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                        <span id="alertMsg">${res.message}</span>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `);
                        }
                    }
                });
            }

            $('#search').on('keypress', function (e) {
                if(e.keyCode == 13) search();
            });

            $('#btnSearch').on('click', function () {
                search();
            })

            $('#company').on('change', function () {
              let selected = $(this).find(':selected').val();
              $('#site').html('');
              $.ajax({
                    type: "GET",
                    url: "{{ url('journal-asset/getsite') }}",
                    data: {
                        company: selected,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function (res) {
                        $('#site').html('<option value="" selected disabled>Pilih Cabang</option>');
                        $.each(res, function (key, val) {
                            $('#site').append('<option value="' + val.si_site + '">' + val.si_site +' - '+ val.si_name +'</option>');
                        });
                    }
                });
              // console.log(selected);
              
            })
        });

    </script>
@stop
