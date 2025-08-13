@extends('adminlte::page')

@section('title', 'Transfer')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Transfer
                    <small>
                        Asset
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Transfer Asset</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <div style="display: inline;">
            @can($menuId . '_create')
                <a href="{{ route('transfer.create') }}" class="btn btn-primary">Add</a>
            @endcan
        </div>
        <br><br>

        <table id="transfer_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>Kode Transfer</th>
                    {{-- <th>Kode Asset</th> --}}
                    <th>Cabang Asal</th>
                    <th>Lokasi Asal</th>
                    <th>Cabang Tujuan</th>
                    <th>Lokasi Tujuan</th>
                    {{-- <th>PIC Tujuan</th> --}}
                    <th>Satus</th>
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
      <h5 class="modal-title">Transfer</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <div class="row">
        <x-adminlte-input name="trf_transno" label="No. Transfer" disabled fgroup-class="col-md-6" />
        <x-adminlte-input name="trf_transdate" label="Tanggal" disabled fgroup-class="col-md-6" />
      </div>

      <div class="row">
        <x-adminlte-input name="trf_site_from" label="Cabang Asal" disabled fgroup-class="col-md-6" />
        <x-adminlte-input name="trf_site_to" label="Cabang tujuan" disabled fgroup-class="col-md-6" />
      </div>

      <div class="row">
        <x-adminlte-input name="trf_loc_from" label="Lokasi Asal" disabled fgroup-class="col-md-6" />
        <x-adminlte-input name="trf_loc_to" label="Lokasi Tujuan" disabled fgroup-class="col-md-6" />
      </div>

      <div class="row">
        <x-adminlte-input name="trf_pic_type_from" label="Tipe PIC Asal" disabled fgroup-class="col-md-6" />
        <x-adminlte-input name="trf_pic_from" label="PIC Asal" disabled fgroup-class="col-md-6" />
      </div>

      <div class="row">
        <x-adminlte-textarea name="remark" fgroup-class="col-md-12" class="form-control"
          label="Keterangan" maxlength="255" disabled />
      </div>
      <div class="row">
        <table class="table table-bordered responsive nowrap" id="trf_dtl">
          <thead>
            <th>No. Asset</th>
            <th>Nama Asset</th>
            <th>Tipe PIC Tujuan</th>
            <th>PIC Tujuan</th>
            <th>Keterangan</th>
          </thead>
          <tbody>
            {{-- <tr>
              <td>
                <x-adminlte-input name="trf_detail_transno" disabled />
              </td>
              <td>
                <x-adminlte-input name="trf_detail_name" disabled />
              </td>
              <td>
                <x-adminlte-input name="trf_detail_desc" disabled />
              </td>
            </tr> --}}
          </tbody>
        </table>
      </div>
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
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
    <style>
        .transno {
            text-align: right;
            float: right;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script>
        let tb;
        let approved = '{{ auth()->user()->can($menuId . '_post') }}';
        function setDatatable(){
            tb = $('#transfer_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                  [5, 'asc'],
                  [6, 'desc'],
                ],
                ajax: "{{ route('transfer.index') }}",
                columns: [
                    {data: 'trf_transno', name: 'trf_transno', width: '4%'},
                    {data: 'trf_site_from', name: 'siteFrom.si_name', className: 'dt-center', width: '5%'},
                    {data: 'trf_loc_from', name: 'locFrom.loc_name', className: 'dt-center', width: '2%'},
                    {data: 'trf_site_to', name: 'siteTo.si_name', className: 'dt-center', width: '5%'},
                    {data: 'trf_loc_to', name: 'locTo.loc_name', className: 'dt-center', width: '5%'},
                    // {data: 'transfer.pic_to', name: 'trf_pic_to', className: 'dt-center', width: '5%'},
                    {data: 'trf_status', name: 'trf_status', className: 'dt-center', width:'7%',
                    render: function(data, type){
                        if(type === 'display'){
                            if(data == "TRF") {
                                return '<span class="badge badge-success">TRF</span>';
                            } else if (data == "DRAFT") {
                                return '<span class="badge badge-primary">DRAFT</span>';
                            } else {
                                return '<span class="badge badge-danger">CANCEL</span>';
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
                          let btn =
                            '<div class="btn-group" role="group"><button class="btn btn-primary" onclick="return showData(\'' +
                            data + '\');" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></button>';
                          
                          @can($menuId . '_update')
                              let editPath = "{{ route('transfer.edit', ':id') }}";
                                  editPath = editPath.replace(':id', data);
                              let edit_disabled = row.trf_status != "DRAFT" ? "d-none" : "";
                              
                              btn += '<a href="' + editPath + '" class="btn btn-warning ' + edit_disabled +
                                '" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a>';
                          @endcan

                          @can($menuId . '_post')
                              let post_disabled = (row.trf_status == 'CANCEL' || row.trf_status == 'TRF') ? 'd-none' : '';
                              btn += '<button class="btn btn-success '+ post_disabled +'" onclick="return acceptRecord(\'' +
                                data + '\', \'' + row.trf_transno +
                                '\');" data-toggle="tooltip" title="POST"><i class="fa fa-paper-plane"></i></button>';
                          @endcan

                          @can($menuId . '_update')
                            let delete_disabled = row.trf_status == 'DRAFT' ? '' : 'd-none';
                            btn += '<button class="btn btn-danger ' + delete_disabled + '" onclick="return toggleState(\'' +
                              data + '\', \'' + row.trf_transno +
                              '\');" data-toggle="tooltip" title="CANCEL"><i class="fa fa-ban"></i></button>';
                          @endcan
                          

                          @can($menuId . '_print')
                              let printPath = "{{ route('transfer.print', ':id') }}";
                                  printPath = printPath.replace(':id', data);
                              // let print_disabled = (row.trf_status == 'TRF' || row.trf_status == 'CANCEL') ? 'd-none' : '';
                              let print_disabled = row.trf_status != 'TRF' ? 'd-none' : '';
                              
                              btn +=
                                  '<a href="'+ printPath +'" class="btn btn-secondary '+ print_disabled +'" data-toggle="tooltip" target="_blank" title="Print"><i class="fa fa-print"></i></a>';
                          @endcan
                          
                          btn += '</div>';
                          return btn;

                        }
                    },
                ],
                search: {
                    "regex": true
                },
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

        // $('#removeRecord').on('click', function () {
        //     Swal.fire({
        //         title: 'Are you sure?',
        //         text: "You won't be able to revert this!",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Yes, delete it!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Deleted!',
        //                 text: 'Your file has been deleted.',
        //                 showConfirmButton: true,
        //             });
        //         }
        //     });
        // })

        function toggleState(id) {
            let url = "{{ route('transfer.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(res) {
                      
                        if (res.res) {
                            toastr.success('Transfer berhasil diupdate', 'Success');
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
            });
        }

        function showData(id) {
          $('#load-overlay').show();
          $('#modal-detail').modal('show');
          $('#modal-detail input').val(null);
          $('#modal-detail textarea').html(null);
          $('#modal-detail span').html(null);
          $('#modal-detail .file').html(null);

          let showPath = "{{ route('transfer.show', ':id') }}";
              showPath = showPath.replace(':id', id);

          $.ajax({
            type: 'GET',
            url: showPath,
            success: function(res) {
              if (res.res) {
                $('#load-overlay').hide();
                
                $('#trf_transno').val(res.data.trf_transno);
                $('#trf_transdate').val(moment(res.data.trf_transdate).format('DD MMM yyyy'));
                $('#trf_site_from').val(res.data.site_from.si_name);
                $('#trf_loc_from').val(res.data.loc_from.loc_name);
                $('#trf_site_to').val(res.data.site_to.si_name);
                $('#trf_loc_to').val(res.data.loc_to.loc_name);
                $('#trf_pic_type_from').val(res.data.trf_pic_type_from.toUpperCase());            
                $('#trf_pic_from').val(res.data.user_from?res.data.user_from.pic_name:res.data.site_from.si_name);
                $('#trf_pic_to').val(res.data.user_to?res.data.user_to.pic_name:res.data.site_to.si_name);
                $('#remark').val(res.data.trf_desc);
                console.log(res.data);
                
                $('#trf_dtl tbody').empty();

                res.data.detail.forEach(val => {
                  
                  let desc = val.trfdtl_desc ? val.trfdtl_desc : '';
                  let row = `
                    <tr>
                      <td>${val.trfdtl_transno}</td>
                      <td>${val.trfdtl_name}</td>
                      <td>${val.trfdtl_pic_type_to.toUpperCase()}</td>
                      <td>${val.trfdtl_pic_type_to == "user" ? val.pic_to.pic_name : val.site_to.si_name}</td>
                      <td>${desc}</td>
                    </tr>
                  `;

                  $('#trf_dtl tbody').append(row);
                });

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

        function acceptRecord(id) {
          let url = "{{ route('transfer.accept', ['id' => ':id']) }}";
          url = url.replace(':id', id);
            Swal.fire({
                title: 'Are you sure approved this?',
                text: "You won't be able to modify or delete data once it's approved.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then((result) => {
                if (result.isConfirmed) {
                  $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        "_token": "{{ csrf_token() }}",
                    },
                    success: function(res) {
                      
                        if (res.res) {
                            toastr.success('Transfer berhasil diApprove', 'Success');
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
            });
        }

    </script>
@stop
