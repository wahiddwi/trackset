@extends('adminlte::page')

@section('title', 'Journal Logs')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Journal
                    <small>
                        Logs
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Journal Logs</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table id="log_table" class="table table-bordered display responsive nowrap" style="width: 100%;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>url</th>
                    <th>Response code</th>
                    <th>Response</th>
                    <th>Logs</th>
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

@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script>
        let tb;
        function setDatatable(){
            tb = $('#log_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('logs') }}",
                aoColumns : [
                    { sWidth: '5%' },
                    { sWidth: '10%' },
                    { sWidth: '5%' },
                    { sWidth: '10%' },
                    { sWidth: '10%' },
                    { sWidth: '5%' },
                ],
                columns: [
                    {data: 'id', name: 'id',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {data: 'url', name: 'url', className: 'dt-center'},
                    {data: 'response_code', name: 'response_code', className: 'text-center',
                        render: function (data) {
                            if (data == 201) {
                                return '<span class="badge badge-success">'+data+'</span>';
                            } else {
                                return '<span class="badge badge-danger">'+data+'</span>';
                            }
                        }
                    },
                    {data: 'response', name: 'response', className: 'dt-center'},
                    {data: 'logs', name: 'logs', width: '2px'},
                    {data: 'updated_at', name: 'updated_at',
                    render: function(data){
                        return moment(data).format('DD MMM yyyy');
                    }},
                ],
                oLanguage: {sProcessing:
                    '<span class="info-box-icon">' +
                        '<img src="{{ asset("assets/img/RGLogo.png") }}" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" width="200" alt="">' +
                    '</span>' +
                    '<div class="info-box-content">' +
                        '<span class="info-box-text">Loading</span>' +
                    '</div>'
                }
            });
        }

        $(function(){
            setDatatable();
        });
    </script>
@stop
