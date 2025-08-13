@extends('adminlte::page')

@section('title', 'Master Asset')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Master
                    <small>
                        Asset
                        <span class="badge badge-primary">{{ $count }}</span>
                    </small>
                </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Asset</li>
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
                <a href="{{ route('asset.create') }}" class="btn btn-primary">Add</a>
                <button class="btn btn-primary" onclick="showModalImport()" data-toggle="tooltip" title="Import"><i class="fas fa-solid fa-upload"></i></button>
            @endcan
        </div>
        <br><br>

        @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil!</strong> {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ Session::get('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <table id="asset_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>Kode Asset</th>
                    <th>Nama</th>
                    <th>Tanggal Pembelian</th>
                    <th>Cabang</th>
                    <th>Lokasi</th>
                    <th>PIC</th>
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

<div class="modal fade" id="showAssetModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pembelian Asset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped responsive">
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>Tgl. Perolehan</strong></td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 49%; text-align: center;"><strong>Tgl. Perolehan</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>Cabang</strong></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;"><strong>Lokasi</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;" id="inv_site"></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;" id="inv_loc"></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>PIC</strong></td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 49%; text-align: center;" id="inv_pic"></td>

                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>Description</strong></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;"><strong></strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%;" colspan="3" id="inv_desc"></td>
                    </tr>
                </table>

                <h6>Details</h6>
                <table id="detail" class="table table-striped table-bordered responsive">
                    <thead>
                        <th>No. Asset</th>
                        <th>Name</th>
                        <th>Tipe</th>
                        <th>Status</th>
                        <th>Harga</th>
                    </thead>
                    <tbody id="detailList">
                    </tbody>
                </table>
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

<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="asset_transno">
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <x-adminlte-input name="asset_name" label="Nama Asset" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="asset_category" label="Kategori" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="asset_merk" label="Merk" disabled fgroup-class="col-md-4" />
        </div>
        
        <div class="row">
          {{-- <x-adminlte-input name="asset_transno" label="Asset No." disabled fgroup-class="col-md-3" /> --}}
          <x-adminlte-input name="asset_site" label="Cabang" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="asset_loc" label="Lokasi" disabled fgroup-class="col-md-4" />
          <x-adminlte-input name="asset_obtaindate" label="Tanggal Perolehan" disabled fgroup-class="col-md-4" />
        </div>

        <div class="row">
          <x-adminlte-input name="asset_sn" label="Imei/SN" disabled fgroup-class="col-md-6" />
          <x-adminlte-input name="asset_doc_ref" label="No. Doc. Referensi" disabled fgroup-class="col-md-6" />
        </div>

        <div class="row">
          <x-adminlte-input name="asset_price" label="Harga Awal" disabled fgroup-class="col-md-6" />
          <x-adminlte-input name="asset_current_price" label="Harga Saat Ini" disabled fgroup-class="col-md-6" />
        </div>

        <div class="row">
          <x-adminlte-input name="asset_type_pic" label="Tipe PIC" disabled fgroup-class="col-md-6" />
          <x-adminlte-input name="asset_pic" label="PIC" disabled fgroup-class="col-md-6" />
        </div>
        
        <div class="row">
          <x-adminlte-input name="asset_tag" label="Tag" disabled fgroup-class="col-md-6" />
          <x-adminlte-textarea name="remark" fgroup-class="col-md-6" class="form-control" label="Keterangan"
            maxlength="255" disabled/>
        </div>
        <div class="file"></div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>

      <div class="overlay" id="load-overlay-dtl">
        <i class="fas fa-2x fa-sync-alt fa-spin"></i>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="showImportModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Asset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="importForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                  <div class="row">
                    <div class="btn-group col-md-8" role="group">
                      <a href="{{ route('asset.download') }}" class="btn btn-primary" data-toggle="tooltip"
                        title="Download Template"><i class="fa-solid fa-download"></i> Template</a>
                      <a href="{{ route('asset.download-tag') }}" class="btn btn-primary" data-toggle="tooltip"
                        title="Download Master Tag"><i class="fa-solid fa-download"></i> Tag</a>
                      <a href="{{ route('asset.download-merk') }}" class="btn btn-primary" data-toggle="tooltip"
                        title="Download Master Merk"><i class="fa-solid fa-download"></i> Merk</a>
                    </div>
                  </div>
                    <div class="row mt-3">
                      <div class="col-md-12">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" id="file_upload" name="file">
                          <label class="custom-file-label" for="file_upload">Upload Asset</label>
                        </div>
                      </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <x-adminlte-button theme="primary" label="Import" id="btnImport" type="submit" icon="fas fa-lg fa-save" />
                </div>
                <div class="overlay" id="load-overlay-imp" hidden>
                  <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </form>
        </div>
        <div class="overlay" id="load-overlay-imp" hidden>
          <i class="fas fa-2x fa-sync-alt fa-spin"></i>
        </div>
    </div>
</div>

@stop
@section('plugins.KrajeeFileinput', true)
@section('plugins.Datatables', true)
@section('plugins.TempusDominusBs4', true)


@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
    <style>
        .btn-group {
            text-align: right;
            float: right;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script> --}}
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/autoNumeric/autoNumeric.min.js') }}"></script>

    <script>
        let tb;
        let approved = '{{ auth()->user()->can($menuId . '_post') }}';
        function setDatatable(){
            tb = $('#asset_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                  [6, 'ASC'],
                  [0, 'DESC']
                ],
                ajax: "{{ route('asset.index') }}",
                createdRow: function (row, data, idx) {
                    if (data.purchase_status == "DRAFT") {
                        $(row).addClass('row-disabled')
                    }
                },
                columns: [
                    {data: 'inv_transno', name: 'inv_transno', width: '4%'},
                    {data: 'inv_name', name: 'inv_name', width: '4%'},
                    {data: 'inv_obtaindate', name: 'inv_obtaindate', className: 'dt-center', width: '2%',
                        render: function (data) {
                            return moment(data).format('DD MMM YYYY')
                    }},
                    {data: 'inv_site', name: 'site.si_name', className: 'dt-center', width: '5%'},
                    {data: 'inv_loc', name: 'location.loc_name', className: 'dt-center', width: '5%'},
                    {data: 'pic', name: 'pic.pic_name', className: 'dt-center', width: '5%'},
                    {data: 'inv_status', name: 'inv_status', className: 'dt-center', width:'7%',
                    render: function(data, type){
                        if(type === 'display'){
                            if(data == "DRAFT") {
                                return '<span class="badge badge-primary">DRAFT</span>';
                            }
                            else if (data == "ONHAND") {
                              return '<span class="badge badge-success">ONHAND</span>';
                            } else if (data == "TRF") {
                              return '<span class="badge badge-warning">TRF</span>';
                            } else if (data == "CANCEL") {
                              return '<span class="badge badge-danger">CANCEL</span>';
                            } else {
                              return '<span class="badge badge-secondary">RSV</span>';
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
                        width: '10%',
                        orderable: false,
                        filterable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let btn =
                                    '<div class="btn-group" role="group"><button class="btn btn-info" onclick="return showData(\'' + data + '\');" data-toggle="tooltip" title="DETAIL"><i class="fa fa-desktop"></i></button> ';
                            @can(session('menuId') . '_update')
                                let editPath = "{{ route('asset.edit', ':id') }}";
                                    editPath = editPath.replace(':id', data);
                                
                                let update_disabled = (row.inv_status == "ONHAND" || row.inv_status == "DRAFT") ? "" : "d-none";
                                btn += '<a href="' + editPath + '" class="btn btn-warning ' + update_disabled + '" data-toggle="tooltip" title="EDIT"><i class="fa fa-pen"></i></a> '
                            @endcan
                            @can(session('menuId') . '_post')
                                let post_disabled = row.inv_status != "DRAFT" ? "d-none" : "";
                                btn += '<button class="btn btn-success '+ post_disabled +'" onclick="return acceptRecord(\'' + data + '\');" data-toggle="tooltip" title="POSTING"><i class="fa fa-check"></i></button>';
                            @endcan
                            @can(session('menuId') . '_print')
                                let qrPath = "{{ route('asset.qr', ':id') }}";
                                        qrPath = qrPath.replace(':id', data);
                                let qr_disabled = row.inv_status != "ONHAND" ? "d-none" : "";
                                btn += '<a href="' + qrPath + '" class="btn btn-primary ' + qr_disabled + '" target="_blank" data-toggle="tooltip" title="QR"><i class="fa-solid fa-qrcode"></i></a>';

                                let barcodePath = "{{ route('asset.barcode', ':id') }}";
                                        barcodePath = barcodePath.replace(':id', data);
                                let barcode_disabled = row.inv_status != "ONHAND" ? "d-none" : "";
                                btn += '<a href="' + barcodePath + '" class="btn ' + barcode_disabled + '" style="background-color: #d3d3d3; color: black;" target="_blank" data-toggle="tooltip" title="Barcode"><i class="fa-solid fa-barcode"></i></a>';
                            @endcan

                            @can(session('menuId') . '_delete')
                                let remove_disabled = row.inv_status != "CANCEL" ? "" : "d-none";

                                btn += '<button class="btn btn-danger '+ remove_disabled +'" onclick="return deleteRecord(\'' + data + '\');" data-toggle="tooltip" title="DELETE"><i class="fa fa-trash"></i></button>';
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

        function showModalImport() {
            $('#showImportModal').modal('show');
        }

        function showModalHandler(id) {
            $('#showAssetModal').modal('show');
            $('#load-overlay').show();
            $('a#printDetail').attr('href', '{{url('asset')}}/' + id + '/print-detail');

            $.ajax({
                type: "GET",
                url: "/asset/"+id,
                success: function (res) {

                    $('#load-overlay').hide();
                    let disabled = '{{ auth()->user()->can($menuId . '_update') }}' ? '' : 'disabled';
                    let print = '{{ auth()->user()->can($menuId . '_print') }}' ? '' : 'disabled';
                    let statusPosting = res.inv_status != 'ONHAND' ? 'disabled' : '';
                    $('a#printDetail').addClass(statusPosting);

                    $('#inv_date').text(moment(res.asset.inv_obtaindate).format('DD MMM YYYY'));
                    $('#inv_site').text(res.asset.site.si_name);
                    $('#inv_loc').text(res.asset.location.loc_name);
                    $('#inv_pic').text(res.asset.user != null && res.asset.inv_pic == res.asset.user.usr_nik  ? res.asset.user.usr_name : res.asset.site.si_name);
                    $('#inv_desc').text(res.asset.inv_desc);

                    var elements = '';
                    let price = new Intl.NumberFormat("id-ID", {
                            style: "currency",
                            "currency": "IDR"
                        }).format(res.history.invhist_price);

                        var code = res.history.invhist_transno;
                        var name = res.history.invhist_name;
                        var category = res.history.category.cat_name;
                        var status = res.history.invhist_status == 'ONHAND' ? '<span class="badge badge-success">'+ res.history.invhist_status +'</span>' : '<span class="badge badge-warning">'+ res.history.invhist_status +'</span>';
                        var inv_price = price;
                        var url = "{{ url('aseet') }}/"+ res.history.id +"/barcode";

                        elements += '<tr>';
                        elements += '<td>'+ code +'</td>';
                        elements += '<td>'+ name +'</td>';
                        elements += '<td>'+ category +'</td>';
                        elements += '<td>'+ status +'</td>';
                        elements += '<td>'+ inv_price +'</td>';
                        elements += '</tr>';

                        $('#detailList').html(elements);
                }
            });
        }

        function acceptRecord(id) {
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
                        type: "GET",
                        url: `{{ url('asset') }}/${id}/accept`,
                        dataType: "json",
                        success: function (res) {
                            if (res.res) {
                                toastr.success('Asset berhasil ditambahkan', 'Success');
                                tb.fnDraw();
                            } else {
                                toastr.error('mohon coba sesaat lagi.', 'Error');
                            }
                        }
                    });
                }
            });
        }

        function deleteRecord(id) {
          Swal.fire({
            title: 'Are you sure remove this?',
            text: "You won't be able to modify once it's removed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes!'
          }).then((result) => {
              if (result.isConfirmed) {
                  $.ajax({
                      type: "POST",
                      url: `{{ url('asset') }}/${id}/remove`,
                      data: {
                        id: id,
                        _token: "{{ csrf_token() }}",
                      },
                      dataType: "json",
                      success: function (res) {
                          if (res.res) {
                              toastr.success('Asset berhasil dihapus', 'Success');
                              tb.fnDraw();
                          } else {
                              toastr.error('mohon coba sesaat lagi.', 'Error');
                          }
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

          let showPath = "{{ route('asset.show', ':id') }}";
              showPath = showPath.replace(':id', id);

          $.ajax({
            type: 'GET',
            url: showPath,
            success: function(res) {
              
              if (res.res) {
                $('#load-overlay-dtl').hide();

                $('#asset_transno').html(res.asset.inv_transno);
                $('#asset_site').val(res.asset.site.si_name);
                $('#asset_loc').val(res.asset.location.loc_name);
                $('#asset_obtaindate').val(moment(res.asset.inv_obtaindate).format('DD MMM YYYY'));
                $('#asset_name').val(res.asset.inv_name);
                $('#asset_category').val(res.asset.category.cat_name);
                $('#asset_merk').val(res.asset.merk ? res.asset.merk.brand_name : '');
                $('#asset_sn').val(res.asset.inv_sn);
                $('#asset_doc_ref').val(res.asset.inv_doc_ref);
                $('#asset_tag').val(res.asset.tag?res.asset.tag.tag_name:'');
                $('#asset_price').val(AutoNumeric.format(res.asset.inv_price, {
                  currencySymbol: 'Rp. ',
                  allowDecimalPadding: 'floats',
                }));
                $('#asset_current_price').val(AutoNumeric.format(res.asset.inv_current_price, {
                  currencySymbol: 'Rp. ',
                  allowDecimalPadding: 'floats',
                }));
                $('#asset_type_pic').val(res.asset.inv_pic_type.toUpperCase());
                if (res.asset.user != null) {
                  $('#asset_pic').val(res.asset.user.usr_name);
                } else {
                  $('#asset_pic').val(res.asset.site.si_name);
                }
                $('#remark').val(res.asset.inv_desc);

                // show uploaded files
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

        $('#btnImport').on('click', function () {
            $('#load-overlay-imp').attr('hidden', false);
        })

      $('#importForm').submit(async function (e) {
        e.preventDefault();

        const url = "{{ route('asset.import') }}";
        const files = $('#file_upload')[0].files;

        if (files.length > 0) {
          const fd = new FormData();
          fd.append('file', files[0]);
          fd.append('_token', '{{ csrf_token() }}');

          $('#load-overlay-imp').attr('hidden', false);

          try {
            const response = await fetch(url, {
              method: 'POST',
              body: fd
            });

            const contentType = response.headers.get('content-type') || '';

            // jika response JSON (validasi gagal/ error)
            if (contentType.includes('application/json')) {
              const result = await response.json();
              $('#load-overlay-imp').attr('hidden', true);

              if (result.res === false) {
                const messages = result.msg.split(';').filter(Boolean);
                messages.forEach(msg => {
                  toastr.error(msg.trim(), 'Error!', {
                    timeOut: 5000,
                    preventDuplicates: true,
                    positionClass: 'toast-top-right'
                  });
                });
              }
            }

            // jika response Blob (berhasil - Excel)
            else if (contentType.includes('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')) {
              const blob = await response.blob();
              $('#load-overlay-imp').attr('hidden', true);

              const disposition = response.headers.get('content-disposition');
              const filenameMatch = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
              const filename = filenameMatch ? filenameMatch[1].replace(/['"]/g, '') : 'result.xlsx';

              const link = document.createElement('a');
              link.href = window.URL.createObjectURL(blob);
              link.download = filename;
              document.body.appendChild(link);
              link.click();
              document.body.removeChild(link);

              toastr.success('Asset berhasil ditambahkan', 'Success', {
                timeOut: 1000,
                preventDuplicates: true,
                positionClass: 'toast-top-right',
                onHidden: function () {
                  window.location.reload();
                }
              });
            }

            // jika tidak dikenali
            else {
              $('#load-overlay-imp').attr('hidden', true);
              toastr.error('Response dari server tidak dikenali.', 'Error!', {
                timeOut: 5000
              });
            }

          } catch (error) {
            $('#load-overlay-imp').attr('hidden', true);
            toastr.error('Terjadi kesalahan saat menghubungi server.', 'Error!', {
              timeOut: 5000
            });
            console.error('Fetch error:', error);
          }
        }
      });
    </script>
@stop
