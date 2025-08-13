@extends('adminlte::page')

@section('title', 'Depreciation List')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Depreciation List
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Depreciation List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
            @can($menuId . '_create')
                <a href="{{ route('depre.create') }}" class="btn btn-primary">Calculate</a> <br><br>
            @endcan

        <table id="dep_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>No. Depresiasi</th>
                    <th>PT</th>
                    <th>Tanggal Depresiasi</th>
                    <th>Status</th>
                    <th>&nbsp;</th>
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
        let tb;
        function setDatatable(){
            tb = $('#dep_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[3, 'asc']],
                ajax: "{{ route('depre.index') }}",
                columns: [
                    {data: 'dep_doc_ref', name: 'dep_doc_ref', width: '4%'},
                    {data: 'dep_company', name: 'company.co_name', width: '4%'},
                    {data: 'dep_eff_date', name: 'dep_eff_date', width: '4%', className: 'dt-center', 
                      render: function (data) {                        
                        return moment(data).format('DD MMM yyyy');
                      }
                    },
                    {data: 'dep_status', name: 'dep_status', className: 'dt-center', width:'7%',
                    render: function(data, type){                      
                        if(type === 'display'){
                          
                            if(data == 'OPEN') {
                                return '<span class="badge badge-primary">OPEN</span>';
                            }
                            else{
                                return '<span class="badge badge-success">DONE</span>';
                            }
                        }
                        return data;
                    }},
                    {
                        data: 'id',
                        width: '1%',
                        orderable: false,
                        filterable: false,
                        searchable: false,
                        render: function(data, type, row) {
                          const canCreateJournal = @json(auth()->user()->can(session('menuId') . '_post'));

                          if (!canCreateJournal) return '';
                          
                          let createJournalPath = "{{ route('depre.journal', ':id') }}";
                              createJournalPath = createJournalPath.replace(':id', data);

                          let disabledJournal = row.dep_status == "OPEN" ? "" : "d-none";

                          return `<div class="btn-group">
                                    <a href="${createJournalPath}" class="btn btn-primary ${disabledJournal}" data-toggle="tooltip" title="CREATE JOURNAL">
                                      <i class="fa-solid fa-file-pen"></i>
                                    </a>
                                  </div>
                                  `;
                        }
                    },
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
