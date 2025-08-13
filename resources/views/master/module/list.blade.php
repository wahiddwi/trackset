@extends('adminlte::page')

@section('title', 'Master Modules')

@section('content_header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
            <h1 class="m-0 text-dark">
                Master Data
                <small>
                    Modules
                    <span class="badge badge-primary">{{ $count }}</span>
                </small>
            </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Master Modules</li>
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
        @can($menuId . '_create')
            <a href="{{route('modules.create')}}" class="btn btn-primary">Add</a><br/><br/>
        @endcan
        <table id="module_table" class="table table-bordered diplay responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Module Name</th>
                    <th>Module Description</th>
                    <th>Module</th>
                    <th>Status</th>
                    <th>Update</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

@stop

@section('css')

@stop

@section('js')
<script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
<script>
    let tb;
    function setDatatable(){
        tb = $('#module_table').dataTable({
            scrollX: true,
            processing: true,
            serverSide: true,
            destroy: true,
            order: [[4, 'desc']],
            ajax: "{{ route('modules.index') }}",
            columns: [
                {data: 'mod_code', name: 'mod_code'},
                {data: 'mod_name', name: 'mod_name'},
                {data: 'mod_desc', name: 'mod_desc'},
                {data: 'mod_path', name: 'mod_path'},
                {data: 'mod_active', className: 'dt-center', width:'7%', 
                render: function(data, type){
                    if(type === 'display'){
                        if(data) {
                            return '<span class="badge badge-primary">Active</span>';
                        }
                        else{
                            return '<span class="badge badge-danger">Inactive</span>';
                        }
                    }
                    return data;
                }},
                {data: 'updated_at', name: 'updated_at', width:'15%', 
                render: function(data){
                   return moment(data).format('DD MMM yyyy'); 
                }}, 
                {data: 'mod_id', width:'1%', orderable: false, 
                render: function(data, _, row){
                    let disabled = '{{auth()->user()->can($menuId . '_update')}}' ? '' : ' d-none';

                    return '<div class="btn-group">' +
                        '<a href="{{url('modules')}}/' + data + '/show" class="btn btn-primary" data-toggle="tooltip" title="View"><i class="fa fa-desktop"></i></a>'+
                        '<a href="{{url('modules')}}/' + data + '/edit" class="btn btn-secondary' + disabled + '" data-toggle="tooltip" title="Edit""><i class="fa fa-pen"></i></a>'+
                        '<button onclick="return toggleState(' + data + ');" class="btn btn-' + (row.mod_active ? "danger" : "primary") + disabled + '" data-toggle="tooltip" title="'+ (row.mod_active ? "Disable" : "Enable") +'"><i class="fa ' + (row.mod_active ? "fa-trash" : "fa-check") + '"></i></button>'
                    '</div>';
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

    function toggleState(id) {
      let url = "{{ route('modules.toggle', ['id' => ':id']) }}";
      url = url.replace(':id', id);
      $.ajax({
          type:'POST',
          url: url,
          data: {
            "_token": "{{ csrf_token() }}",
          },
          success:function(res) {
              if(res.res){
                  toastr.success('Module berhasil diupdate', 'Success');
                  tb.fnDraw();
              }
              else{
                  toastr.error('mohon coba sesaat lagi.', 'Error');
              }
          },
          error:function (xhr, ajaxOptions, thrownError) {
              toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
          }
      });
    }

    $(function(){
        setDatatable();        
    });
</script>
@stop