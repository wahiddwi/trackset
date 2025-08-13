@extends('adminlte::page')

@section('title', 'Transfer - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Transfer
                {{-- <small>
                    Asset
                </small> --}}
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('transfer.index')}}">Transfer</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@stop

@section('plugins.Datatables', true)

@section('content')
<form action="{{ route('transfer.store')}}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header"><h5>Cabang & Lokasi</h5></div>
        <div class="card-body">
            <div class="row">
                <input type="hidden" value="" id="siteFromHidden">
                <input type="hidden" value="" id="locFromHidden">
                <input type="hidden" value="" id="picFromHidden">

                <x-adminlte-select2 name="trf_site_from" id="siteFrom" label="Cabang Asal" fgroup-class="col-md-6" error-key="trf_site_from" class="form-control siteFrom">
                    <option value="" selected disabled>Pilih Cabang Asal</option>
                    @foreach ($sites as $site)
                        <option value="{{$site->si_site}}">{{ $site->si_name }}</option>
                    @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="trf_loc_from" id="locFrom" label="Lokasi Asal" fgroup-class="col-md-6" error-key="trf_loc_from" class="form-control locFrom">
                    <option value="" selected disabled>Pilih Lokasi Asal</option>
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-select2 name="trf_site_to" id="siteTo" label="Cabang Tujuan" fgroup-class="col-md-6" error-key="trf_site_to" class="form-control">
                    <option value="" selected disabled>Pilih Cabang Tujuan</option>
                    @foreach ($sites as $site)
                        <option value="{{$site->si_site}}">{{ $site->si_name }}</option>
                    @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="trf_loc_to" id="locTo" label="Lokasi Tujuan" fgroup-class="col-md-6" error-key="trf_loc_to" class="form-control">
                    <option value="" selected disabled>Pilih Lokasi Tujuan</option>
                </x-adminlte-select2>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>PIC Asal & PIC Tujuan</h5></div>
        <div class="card-body">
            <div class="row">
                <x-adminlte-select name="pic_type_from" label="Tipe PIC Asal" fgroup-class="col-md-6" error-key="pic_type_from" id="picTypeFrom" class="form-control">
                    <option value="" selected disabled>Pilih PIC type Asal</option>
                    <option value="user">User</option>
                    <option value="cabang">Cabang</option>
                </x-adminlte-select>

                <x-adminlte-select2 name="trf_pic_from" label="PIC Asal" fgroup-class="col-md-6" error-key="trf_pic_from" id="picFrom" class="form-control">
                    <option value="" selected disabled>Pilih PIC Asal</option>
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-select name="pic_type_to" label="Tipe PIC Tujuan" fgroup-class="col-md-6" error-key="pic_type_to" id="picTypeTo" class="form-control">
                    <option value="" selected disabled>Pilih PIC type Tujuan</option>
                    <option value="user">User</option>
                    <option value="cabang">Cabang</option>
                </x-adminlte-select>

                <x-adminlte-select2 name="trf_pic_to" label="PIC Tujuan" fgroup-class="col-md-6" error-key="trf_pic_to" id="picTo" class="form-control">
                    <option value="" selected disabled>Pilih PIC Tujuan</option>
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-input name="search" label="Search" placeholder="search...." id="searchValue" igroup-size="md" fgroup-class="col-md-8" error-key="search">
                    <x-slot name="appendSlot">
                        <a href="javascript:" type="search" class="btn btn-sm btn-outline-primary" id="btnSearch">Go!</a>
                    </x-slot>
                    <x-slot name="prependSlot">
                        <div class="input-group-text text-primary">
                            <i class="fas fa-search"></i>
                        </div>
                    </x-slot>
                </x-adminlte-input>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body" id="cardItems">
            <div class="row">
                <h5>Items :</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            {{-- <th>#</th> --}}
                            <th>Items</th>
                            <th>No. Assets</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('transfer.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')

@stop

@section('js')
    <script>
        $(document).ready(function () {
            $('#cardItems').hide();
            $('.addRecord').on('click', function () {
                addDetail();
            });

            $('#siteFrom').on('change', function () {
                var siteFromId = this.value;
                $('#siteFromHidden').val($(this).val());
                $('#locFrom').html('');
                $.ajax({
                    type: "POST",
                    url: "{{ url('transfer/getlocation') }}",
                    data: {
                        loc_site: siteFromId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function (res) {
                        $('#locFrom').html('<option value="" selected disabled>Pilih Lokasi Asal</option>');
                        $.each(res, function (key, val) {
                            $('#locFrom').append('<option value="' + val.id + '">' + val.loc_name + '</option>');
                        });
                    }
                });
            });

            $('#siteTo').on('change', function () {
                var siteToId = this.value;
                $('#locTo').html('');
                $.ajax({
                    type: "POST",
                    url: "{{ url('transfer/getlocation') }}",
                    data: {
                        loc_site: siteToId,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function (res) {
                        $('#locTo').html('<option value="" selected disabled>Pilih Lokasi Tujuan</option>');
                        $.each(res, function (key, val) {
                            $('#locTo').append('<option value="' + val.id + '">' + val.loc_name + '</option>');
                        });
                    }
                });
            });

            $('#picTypeFrom').on('change', function () {
                $.ajax({
                    type: "GET",
                    url: "/transfer/get-type",
                    dataType: "json",
                    success: function (res) {
                        var users = res.user;
                        var sites = res.site;
                        var picTypeFrom = $('#picTypeFrom option:selected').val();

                        if (picTypeFrom == "user") {
                            $('#picFrom option').remove();
                            $.each(users, function (i, user) {
                                console.log(user);
                                $('#picFrom').append($('<option>', {
                                    value: user.usr_nik,
                                    text: user.usr_name
                                }));
                            });
                        } else if (picTypeFrom == "cabang") {
                            $('#picFrom option').remove();
                            $.each(sites, function (i, site) {
                                $('#picFrom').append($('<option>', {
                                    value: site.si_site,
                                    text: site.si_name
                                }));
                            });
                        }

                    }
                });
            })

            $('#picTypeTo').on('change', function () {
                $.ajax({
                    type: "GET",
                    url: "/transfer/get-type",
                    dataType: "json",
                    success: function (res) {
                        var users = res.user;
                        var sites = res.site;
                        var picTypeTo = $('#picTypeTo option:selected').val();

                        if (picTypeTo == "user") {
                            $('#picTo option').remove();
                            $.each(users, function (i, user) {
                                $('#picTo').append($('<option>', {
                                    value: user.usr_nik,
                                    text: user.usr_name
                                }));
                            });
                        } else if (picTypeTo == "cabang") {
                            $('#picTo option').remove();
                            $.each(sites, function (i, site) {
                                $('#picTo').append($('<option>', {
                                    value: site.si_site,
                                    text: site.si_name
                                }));
                            });
                        }
                    }
                });
            })

            $('#locFrom').on('change', function () {
                $('#locFromHidden').val($(this).val());
            })

            $('#picFrom').on('change', function () {
                $('#picFromHidden').val($(this).val());
            })

            $('#btnSearch').on('click', function () {
                // $('#cardItems').hide();
                let searchValue = $('#searchValue').val();
                var selectSiteFrom = $('#siteFromHidden').val();
                var selectLocFrom = $('#locFromHidden').val();
                var selectPICFrom =  $('#picFromHidden').val();

                $.ajax({
                    type: "GET",
                    url: "{{ url('transfer/search') }}",
                    data: {
                        search: searchValue,
                        siteId: selectSiteFrom,
                        locId: selectLocFrom,
                        picId: selectPICFrom,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function (res) {
                    console.log(res);
                        console.log(res.inv_name);
                        $('#cardItems').show();
                        let searchView = $('#searchView').val(searchValue);
                        let elements = '';

                        // elements += '<tr>';
                        // elements += '<td>'+ res.inv_name +'</td>';
                        // elements += '<td>'+ res.inv_transno +'</td>';
                        // elements += '<td>';
                        // elements += '<a href="javascript:" class="btn btn-danger .removeRecord" id="removeRecord"><i class="fas fa-trash"></i></a>';
                        // elements += '</td>';
                        // elements += '</tr>';

                        elements += '<tr>';
                        elements += '<td>';
                        elements += '<input name="trf_detail_name[]" class="form-control" value="'+ res.inv_name +'" readonly />';
                        elements += '</td>';
                        elements += '<td>';
                        elements += '<input name="trf_detail_transno[]" class="form-control" value="'+ res.inv_transno +'" readonly />';
                        elements += '</td>';
                        elements += '<td>';
                        elements += '<a href="javascript:" class="btn btn-danger .removeRecord" id="removeRecord"><i class="fas fa-trash"></i></a>';
                        elements += '</td>';
                        elements += '</tr>';

                        $('.table').append(elements);
                    }
                });
            });

            $('.table').on('click', '#removeRecord', function () {
                $(this).closest('tr').remove();
            })
        });

    </script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
