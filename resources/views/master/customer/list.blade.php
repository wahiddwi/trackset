@extends('adminlte::page')

@section('title', 'Master Customer')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Master
                    <small>
                        Customer
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Customer</li>
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
                <a href="{{ route('customer.create') }}" class="btn btn-primary">Add</a> <br><br>
            @endcan

        <table id="customer_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Telp</th>
                    <th>Status</th>
                    <th>Update</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          Customer
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          {{-- <x-adminlte-input name="asset_transno" label="Asset No." disabled fgroup-class="col-md-3" /> --}}
          <x-adminlte-input name="cust_name" label="Nama" disabled fgroup-class="col-md-6" />
          <x-adminlte-input name="cust_no" label="No. Identitas" disabled fgroup-class="col-md-6" />
        </div>

        <div class="row">
          <x-adminlte-input name="cust_type" label="Tipe Identitas" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="cust_telp" label="No. Telp" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="cust_internal" label="Customer Internal ?" disabled fgroup-class="col-md-4" />
        </div>

        <div class="row">
          <x-adminlte-textarea name="remark" fgroup-class="col-md-12" class="form-control" label="Alamat"
            maxlength="255" disabled />
        </div>

        {{-- <div class="row col-md-12">
          <small>
            Dibuat oleh <span id="created_user"></span>
          </small>
        </div> --}}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>

      <div class="overlay" id="load-overlay">
        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
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
            tb = $('#customer_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('customer.index') }}",
                columns: [
                    {data: 'cust_no', name: 'cust_no', width: '4%'},
                    {data: 'cust_name', name: 'cust_name', className: 'dt-center'},
                    {data: 'cust_type', name: 'cust_type', className: 'dt-center'},
                    {data: 'cust_telp', name: 'cust_telp', className: 'dt-center'},
                    {data: 'cust_active', name: 'cust_active', className: 'dt-center', width:'7%',
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
                    {data: 'updated_at', name: 'updated_at', width:'10%',
                    render: function(data){
                        return moment(data).format('DD MMM yyyy');
                    }},
                    {
                        data: 'id',
                        width: '1%',
                        orderable: false,
                        filterable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            // let disabled = '{{ auth()->user()->can($menuId . '_update') }}' ? '' : 'd-none';
                            // return '<div class="btn-group">' +
                            //     '<a href="{{ url('cat-depreciations') }}/' + data +
                            //     '/edit" class="btn btn-secondary ' + disabled +
                            //     '" data-toggle="tooltip" title="View"><i class="fa fa-pen"></i></a>' +
                            //     '<button onclick="return toggleState(' + data + ');" class="btn btn-' + (row
                            //         .dep_active ? "danger" : "primary") + disabled +
                            //     '" data-toggle="tooltip" title="' + (row.dep_active ? "Disable" :
                            //         "Enable") + '"><i class="fa ' + (row.dep_active ? "fa-trash" :
                            //         "fa-check") +
                            //     '"></i></button>'
                            // '</div>';
                            
                            let btn =
                                    '<div class="btn-group" role="group"><button class="btn btn-info" onclick="return showData(\'' + data + '\');" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></button> ';
                            @can($menuId . '_update')
                              let editPath = "{{ route('customer.edit', ':id') }}";
                                  editPath = editPath.replace(':id', data);
                              btn += '<a href="'+ editPath +'" class="btn btn-warning" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a>';

                              btn += 
                                      '<button onclick="return toggleState('+ data +');" class="btn btn-' + (row.cust_active ? "danger":"primary") +'" data-toggle="tooltip" title="'+ (row.cust_active ? "Disable" : "Enable") +'"><i class="fa '+ (row.cust_active ? "fa-trash" : "fa-check") +'"></i></button>'
                            @endcan

                            btn += '</div>';

                            return btn;
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

        function toggleState(id) {
            let url = "{{ route('customer.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                    if (res.res) {
                        toastr.success('Customer berhasil diupdate', 'Success');
                        tb.fnDraw();
                    } else {
                        toastr.error('mohon coba sesaat lagi.', 'Error');
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
                }
            });
        }

        function showData(id) {
          // console.log(id);
          $('#load-overlay').show();
          $('#modal-detail').modal('show');
          $('#modal-detail input').val(null);
          $('#modal-detail textarea').html(null);
          $('#modal-detail span').html(null);

          let showPath = "{{ route('customer.show', ':id') }}";
              showPath = showPath.replace(':id', id);

          $.ajax({
            type: "GET",
            url: showPath,
            success: function (res) {
              if (res.res) {
                $('#load-overlay').hide();

                $('#cust_name').val(res.data.cust_name);
                $('#cust_no').val(res.data.cust_no);
                $('#cust_type').val(res.data.cust_type);
                $('#cust_telp').val(res.data.cust_telp);
                $('#cust_internal').val(res.data.cust_internal ? 'YA' : 'Tidak');
                $('#remark').val(res.data.cust_addr);

              } else {
                $('#modal-detail input').val(null);
                $('#modal-detail textarea').html(null);
                $('#modal-detail span').html(null);

                $('#modal-detail').modal('hide');
                toastr.error('mohon coba sesaat lagi.', 'Error');
              }
            }
          });
          
        }
    </script>
@stop
