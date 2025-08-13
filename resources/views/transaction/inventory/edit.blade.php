@extends('adminlte::page')

@section('title', 'Transaksi Inventory - Edit')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Data Transaksi
                <small>
                    Inventory
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('inventory.index')}}">Master Location</a></li>
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
        <form action="{{ route('inventory.update', $data->id)}}" method="POST">
            @csrf
            @method('PATCH')
            <div class="row">
                    <input type="hidden" value="{{ $data->id }}" class="form-control col-md-6" id="invId">

                    <x-adminlte-input name="inv_transno" label="Kode Inventory" placeholder="" maxlength="10" value="{{$data->inv_transno}}"
                        fgroup-class="col-md-6" disabled/>

                        <x-adminlte-input name="invhist_id" label="Kode Inventory History" placeholder="" maxlength="10" value="{{$invHist[0]->invhist_id}}"
                            fgroup-class="col-md-6" disabled/>
            </div>
            <div class="row">
                <x-adminlte-input name="inv_name" label="Name" placeholder="" maxlength="60" value="{{$data->inv_name}}"
                    fgroup-class="col-md-6" error-key="inv_name"/>

                <x-adminlte-select2 name="inv_category" id="category" label="Category" fgroup-class="col-md-6" error-key="inv_category" class="form-control">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $cat->id == $data->inv_category ? 'selected' : '' }}>{{ $cat->cat_name }}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-select2 name="inv_location" id="location" label="Location" fgroup-class="col-md-6" error-key="inv_location" class="form-control">
                    @foreach ($locations as $loc)
                        <option value="{{ $loc->id }}" {{ $loc->id == $data->inv_location ? 'selected' : '' }}>{{ $loc->loc_name }}</option>
                    @endforeach
                </x-adminlte-select2>

                {{-- @dd($invHist[0]); --}}

                <x-adminlte-select2 name="inv_site" id="site" label="Cabang" fgroup-class="col-md-6" error-key="inv_site" class="form-control">
                    <option value="" selected disabled>Pilih Cabang</option>
                    @foreach ($sites as $site)
                        <option value="{{$site->si_site}}" {{ $site->si_site == $invHist[0]->invhist_site ? 'selected' : '' }}>{{ $site->si_name }}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-select name="inv_pic_type" id="picType" label="PIC Type" fgroup-class="col-md-6" error-key="inv_pic_type" class="form-control">
                    @if ($data->inv_pic_type == "user")
                        <option value="{{ $data->inv_pic_type }}" selected>{{ Str::ucfirst($data->inv_pic_type) }}</option>
                        <option value="cabang">Cabang</option>
                    @else
                        <option value="{{ $data->inv_pic_type }}" selected>{{ Str::ucfirst($data->inv_pic_type) }}</option>
                        <option value="user">User</option>
                    @endif
                </x-adminlte-select>

                <x-adminlte-select2 name="inv_pic" label="PIC" fgroup-class="col-md-6" error-key="inv_pic" id="pic" class="form-control">
                    @if ($data->inv_pic_type == "user")
                        @foreach ($users as $user)
                            <option value="{{ $user->usr_nik }}" {{ $user->usr_nik == $data->inv_pic ? 'selected' : ''}}>{{ $user->usr_name }}</option>
                        @endforeach
                    @else
                        @foreach ($sites as $site)
                            <option value="{{ $site->si_site }}" {{ $site->si_site == $data->inv_pic ? 'selected' : ''}}>{{ $site->si_name }}</option>
                        @endforeach
                    @endif
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-input name="inv_obtaindate" label="Obtain Date" placeholder=""
                fgroup-class="col-md-6" error-key="inv_obtaindate" type="date" value="{{$data->inv_obtaindate}}" />

                <x-adminlte-input name="inv_qty" id="inv_qty" label="Quantity" placeholder="" type="number"
                fgroup-class="col-md-6" error-key="inv_qty" min="0" value="{{ $invHist[0]->invhist_qty }}"/>
            </div>

            <div class="row">
                <x-adminlte-input name="inv_price" id="inv_price" label="Price" placeholder="" type="number"
                fgroup-class="col-md-6" error-key="inv_price" value="{{ $data->inv_price }}">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <b>Rp. </b>
                        </div>
                    </x-slot>
                </x-adminlte-input>

                <x-adminlte-select name="inv_status" label="Status" fgroup-class="col-md-6" error-key="inv_status" class="form-control">
                    @if ($data->inv_status == "transit")
                        <option value="{{ $data->inv_status }}" selected>{{ Str::ucfirst($data->inv_status) }}</option>
                        <option value="on hand">On Hand</option>
                    @else
                        <option value="{{ $data->inv_status }}" selected>{{ Str::ucfirst($data->inv_status) }}</option>
                        <option value="transit">Transit</option>
                    @endif
                </x-adminlte-select>
            </div>

            <div class="row">
                <x-adminlte-input name="total_price" id="total_price" label="Total Price" placeholder=""
                fgroup-class="col-md-6" error-key="total_price" value="{{ number_format($invHist[0]->invhist_total_price ,0,',',',') }}" disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <b>Rp. </b>
                        </div>
                    </x-slot>
                </x-adminlte-input>

                <input type="hidden" name="inv_total_price" id="inv_total_price" value="{{ $invHist[0]->invhist_total_price }}">

                <x-adminlte-textarea name="inv_description" label="Description" fgroup-class="col-md-6" rows="5" igroup-size="sm" placeholder="Insert description...">
                    {{ $data->inv_description }}
                </x-adminlte-textarea>
            </div>

            <div class="row btn-group">
                <x-adminlte-button class="btn" type="submit" label="Submit" theme="success" icon="fas fa-lg fa-save"/>
                <a href="{{route('inventory.index')}}" class="btn btn-danger"><i class="fas fa-lg fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('css')

@stop

@section('js')
<script>
    $(document).ready(function () {
        $('#inv_price').keyup(function (e) {
            e.preventDefault();
            let qty = $('#inv_qty').val();
            let total = qty * $('#inv_price').val();
            $('#total_price').val(total.toLocaleString());
            $('#inv_total_price').val(total);
        });

        $('#inv_qty').keyup(function (e) {
            e.preventDefault();
            let price = $('#inv_price').val();
            let total = price * $('#inv_qty').val();
            $('#total_price').val(total.toLocaleString());
            $('#inv_total_price').val(total)
        })

        $('#picType').on('change', function () {
            $.ajax({
                type: "GET",
                url: "/inventory/get-type",
                dataType: "json",
                success: function (res) {
                    var users = res.user;
                    var sites = res.site;
                    var picTypeValue = $('#picType option:selected').val();

                    if (picTypeValue == "user") {
                        $('#pic option').remove();
                        $.each(users, function (i, user) {
                            $('#pic').append($('<option>', {
                                value: user.usr_nik,
                                text: user.usr_name
                            }));
                        });
                    } else if (picTypeValue == "cabang") {
                        $('#pic option').remove();
                        $.each(sites, function (i, site) {
                            $('#pic').append($('<option>', {
                                value: site.si_site,
                                text: site.si_name
                            }));
                        });
                    }

                }
            });
        })

    });
</script>
@stop
@section('plugins.Select2', true)
@section('plugins.BootstrapSwitch', true)
