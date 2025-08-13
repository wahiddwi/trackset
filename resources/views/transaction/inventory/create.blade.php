@extends('adminlte::page')

@section('title', 'Master Inventory - Create')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Inventory
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{route('inventory.index')}}">Master Inventory</a></li>
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
        <form action="{{ route('inventory.store')}}" method="POST">
            @csrf
            <div class="row">
                <x-adminlte-input name="inv_name" label="Name" placeholder=""
                    fgroup-class="col-md-6" error-key="inv_name"/>

                <x-adminlte-select2 name="inv_category" id="category" label="Category" fgroup-class="col-md-6" error-key="inv_category" class="form-control">
                    <option value="" selected disabled>Pilih Category</option>
                    @foreach ($categories as $cat)
                        <option value="{{$cat->id}}">{{ $cat->cat_name }}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-select2 name="inv_location" id="location" label="Location" fgroup-class="col-md-6" error-key="inv_location" class="form-control">
                    <option value="" selected disabled>Pilih Lokasi</option>
                    @foreach ($locations as $loc)
                        <option value="{{$loc->id}}">{{ $loc->loc_name }}</option>
                    @endforeach
                </x-adminlte-select2>

                <x-adminlte-select2 name="inv_site" id="site" label="Cabang" fgroup-class="col-md-6" error-key="inv_site" class="form-control">
                    <option value="" selected disabled>Pilih Cabang</option>
                    @foreach ($sites as $site)
                        <option value="{{$site->si_site}}">{{ $site->si_name }}</option>
                    @endforeach
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-select name="inv_pic_type" label="PIC type" fgroup-class="col-md-6" error-key="inv_pic_type" id="picType" class="form-control">
                    <option value="" selected disabled>Pilih PIC type</option>
                    <option value="user">User</option>
                    <option value="cabang">Cabang</option>
                </x-adminlte-select>

                <x-adminlte-select2 name="inv_pic" label="PIC" fgroup-class="col-md-6" error-key="inv_pic" id="pic" class="form-control">
                    <option value="" selected disabled>Pilih PIC</option>
                </x-adminlte-select2>
            </div>

            <div class="row">
                <x-adminlte-input name="inv_obtaindate" label="Obtain Date" placeholder=""
                fgroup-class="col-md-6" error-key="inv_obtaindate" type="date"/>

                <x-adminlte-input name="inv_qty" id="inv_qty" label="Quantity" placeholder="" type="number"
                fgroup-class="col-md-6" error-key="inv_qty" min="0" />
            </div>

            <div class="row">
                <x-adminlte-input name="inv_price" id="inv_price" label="Price" placeholder="" type="number"
                fgroup-class="col-md-6" error-key="inv_price">
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <b>Rp. </b>
                        </div>
                    </x-slot>
                </x-adminlte-input>

                <x-adminlte-select name="inv_status" label="Status" fgroup-class="col-md-6" error-key="inv_status" class="form-control">
                    <option value="" selected disabled>Pilih Status</option>
                    <option value="on hand">On Hand</option>
                    <option value="transit">Transit</option>
                </x-adminlte-select>
            </div>

            <div class="row">
                <x-adminlte-input name="total_price" id="total_price" label="Total Price" placeholder=""
                fgroup-class="col-md-6" error-key="total_price" disabled>
                    <x-slot name="prependSlot">
                        <div class="input-group-text">
                            <b>Rp. </b>
                        </div>
                    </x-slot>
                </x-adminlte-input>

                <input type="hidden" name="inv_total_price" id="inv_total_price">

                <x-adminlte-textarea name="inv_description" label="Description" fgroup-class="col-md-6" rows="7" igroup-size="sm" placeholder="Insert description..."/>
            </div>

            <hr>

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
                $('#inv_total_price').val(total)
            })

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
