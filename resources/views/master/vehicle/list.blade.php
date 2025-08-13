@extends('adminlte::page')

@section('title', 'Master Kendaraan')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Master Data
                    <small>
                        Vehicle
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Vehicle</li>
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
            <a href="{{ route('vehicle.create') }}" class="btn btn-primary">Add</a><br><br>
        @endcan

        <table id="vehicle_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>No. Kendaraan</th>
                    <th>Brand</th>
                    <th>Warna</th>
                    <th>Silinder</th>
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
          Detail Kendaraan
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <x-adminlte-input name="vehicle_transno" label="No. Asset" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="vehicle_no" label="No. Kendaraan" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="vehicle_coverage" label="Cover Asuransi" disabled fgroup-class="col-md-4" />
        </div>

        <div class="row">
          <x-adminlte-input name="vehicle_identityno" label="No. Rangka" disabled fgroup-class="col-md-6" />
          <x-adminlte-input name="vehicle_engineno" label="No. Mesin" disabled fgroup-class="col-md-6" />
        </div>
        
        <div class="row">
          <x-adminlte-input name="vehicle_documentno" label="No. Dokumen" disabled fgroup-class="col-md-6" />
          <x-adminlte-input name="vehicle_brand" label="Brand" disabled fgroup-class="col-md-6" />
        </div>
        
        <div class="row">
          <x-adminlte-input name="vehicle_color" label="Warna" disabled fgroup-class="col-md-6" />
          <x-adminlte-input name="vehicle_capacity" label="Silinder (CC)" disabled fgroup-class="col-md-6" />
        </div>

        <div class="row">
          <x-adminlte-textarea name="remark" fgroup-class="col-md-12" class="form-control" label="Keterangan"
            maxlength="255" rows="4" disabled />
        </div>
        <div class="file"></div>
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
@section('plugins.KrajeeFileinput', true)

@section('css')

@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script>
        let tb;
        function setDatatable(){
            tb = $('#vehicle_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('vehicle.index') }}",
                columns: [
                    {data: 'vehicle_no', name: 'vehicle_no'},
                    {data: 'vehicle_brand', name: 'brand.brand_name'},
                    {data: 'vehicle_color', name: 'vehicle_color'},
                    {data: 'vehicle_capacity', name: 'vehicle_capacity', 
                      render: function (data) {
                        return data +' CC';
                      }
                    },
                    {data: 'updated_at', name: 'updated_at', width:'15%',
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
                          let detailPath = "{{ route('vehicle.show', ':id') }}";
                              detailPath = detailPath.replace(':id', data);
                          let btn = 
                                    // '<div class="btn-group"><button class="btn btn-info" onclick="return showData(\'' + data + '\');" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></button>';
                                      '<a href="'+ detailPath +'" class="btn btn-info" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></a>';
                          @can(session('menuId') . '_update')
                            let editPath = "{{ route('vehicle.edit', ':id') }}";
                                editPath = editPath.replace(':id', data);
                            btn += '<a href="'+ editPath +'" class="btn btn-primary" data-toggle="tooltip" title="ADD & EDIT"><i class="fa fa-plus"></i></a>';
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

        $('#btnDelete').on('click', function () {

        })

        function showData(id) {
          $('#load-overlay').show();
          $('#modal-detail').modal('show');
          $('#modal-detail input').val(null);
          $('#modal-detail textarea').html(null);
          $('#modal-detail span').html(null);
          $('#modal-detail .file').html(null);

          let showPath = "{{ route('vehicle.show', ':id') }}";
              showPath = showPath.replace(':id', id);

          $.ajax({
            type: "GET",
            url: showPath,
            success: function (res) {
              if (res.res) {
                $('#load-overlay').hide();
                $('#vehicle_no').val(res.data.vehicle_no);
                $('#vehicle_transno').val(res.data.vehicle_transno);
                $('#vehicle_identityno').val(res.data.vehicle_identityno);
                $('#vehicle_engineno').val(res.data.vehicle_engineno);
                $('#vehicle_capacity').val(res.data.vehicle_capacity);
                $('#vehicle_brand').val(res.data.vehicle_brand);
                $('#vehicle_color').val(res.data.vehicle_color);
                // $('#vehicle_coverage').val(res.data.vehicle_coverage);
                $('#vehicle_documentno').val(res.data.vehicle_documentno);
                $('#remark').val(res.data.vehicle_desc);
                // $('#created_user').html(res.data.created_by == res.user);

                // show uploaded files
                console.log(res);
                
                if (res.files.length > 0) {
                  const $input = $('<input type="file" />')
                  $('#modal-detail .file').append($input)

                  $input.fileinput({
                    allowedFileTypes: ['image', 'pdf'],
                    browseOnZoneClick: false,
                    showUpload: false,
                    showRemove: false,
                    overwriteInitial: false,
                    fileActionSettings: {
                      showRemove: true,
                      showRotate: false,
                      showDrag: false
                    },
                    initialPreviewAsData: true,
                    initialPreview: res.files,
                    initialPreviewConfig: res.config
                  });

                  // For "DISABLED" input, but zoom capabilities
                  $input.closest('.file-caption').hide();
                }
              } else {
                $('#modal-detail input').val(null);
                $('#modal-detail textarea').html(null);
                $('#modal-detail span').html(null);

                $('#modal-detail').modal('hide');
                toastr.error('mohon coba sesaat lagi.', 'Error');
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              $('#user_id').val(null);
              $('#modal-detail').modal('hide');
              toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
            }
          });
          
        }
    </script>
@stop
