@extends('adminlte::page')

@section('title', 'Master Site')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Master Data
                        <small>
                            Site
                            <span class="badge badge-primary">{{ $count }}</span>
                        </small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Site</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
          @can($menuId . '_post')
            <a href="{{ route('sites.sync') }}" class="btn btn-primary">Sync</a> <br><br>
          @endcan
            <table id="site_table" class="table table-bordered display responsive nowrap" style="width: 100%">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

@stop
@section('plugins.Datatables', true)
@section('css')
    <style>
        .btn-group {
            text-align: right;
            float: right;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script>
        function setDatatable() {
            let tb = $('#site_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                    [0, 'asc']
                ],
                ajax: "{{ route('sites.index') }}",
                columns: [{
                        data: 'si_site',
                        name: 'si_site'
                    },
                    {
                        data: 'si_name',
                        name: 'si_name'
                    },
                    {
                        data: 'company.co_name',
                        name: 'company.co_name'
                    },
                    {
                        data: 'si_active',
                        name: 'si_active',
                        className: 'dt-center',
                        width: '7%',
                        render: function(data, type) {
                            if (type === 'display') {
                                if (data) {
                                    return '<span class="badge badge-primary">Active</span>';
                                } else {
                                    return '<span class="badge badge-danger">Inactive</span>';
                                }
                            }
                            return data;
                        }
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        width: '15%',
                        render: function(data) {
                            return moment(data).format('DD MMM yyyy');
                        }
                    },
                ],
                oLanguage: {
                    sProcessing: '<span class="info-box-icon">' +
                        '<img src="{{ asset('assets/img/RGLogo.png') }}" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" width="200" alt="">' +
                        '</span>' +
                        '<div class="info-box-content">' +
                        '<span class="info-box-text">Loading</span>' +
                        '</div>'
                }
            });
        }

        $(function() {
            setDatatable();
        });
    </script>
@stop
