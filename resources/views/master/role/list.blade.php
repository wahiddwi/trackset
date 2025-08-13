@extends('adminlte::page')

@section('title', 'Master Roles')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Master Data
                        <small>
                            Roles
                            <span class="badge badge-primary">{{ $count }}</span>
                        </small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Master Roles</li>
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
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">Add</a><br><br>
            @endcan

            <table id="role_table" class="table table-bordered diplay responsive nowrap" style="width:100%;">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Posisi</th>
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

    <!-- Modal -->
    <div class="modal fade" id="modal-privilege" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Privilege</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="privilege-form">
                      @csrf
                      <table class="table table-hover table-sm diplay responsive nowrap" style="width:100%">
                        <thead>
                          <tr>
                            <th>Menu</th>
                            <th>Desc</th>
                            <th class="text-center">
                                <label class="switch">
                                    <input type="checkbox" class="check-all" data-target="view">
                                    <span class="slider round"></span>
                                </label> View
                            </th>
                            <th class="text-center">
                                <label class="switch">
                                    <input type="checkbox" class="check-all" data-target="create">
                                    <span class="slider round"></span>
                                </label> Create
                            </th>
                            <th class="text-center">
                                <label class="switch">
                                    <input type="checkbox" class="check-all" data-target="update">
                                    <span class="slider round"></span>
                                </label> Update
                            </th>
                            <th class="text-center">
                                <label class="switch">
                                    <input type="checkbox" class="check-all" data-target="print">
                                    <span class="slider round"></span>
                                </label> Print
                            </th>
                            <th class="text-center">
                                <label class="switch">
                                    <input type="checkbox" class="check-all" data-target="post">
                                    <span class="slider round"></span>
                                </label> Post
                            </th>
                            <th class="text-center">
                                <label class="switch">
                                    <input type="checkbox" class="check-all" data-target="delete">
                                    <span class="slider round"></span>
                                </label> Delete
                            </th>
                          </tr>
                        </thead>
                      
                          <tbody>
                              @foreach ($modules as $mdl)
                                  <?php $disable_upd = auth()
                                      ->user()
                                      ->can($menuId . '_update')
                                      ? ''
                                      : ' disabled'; ?>
                                  <tr>
                                      <td><b>{{ $mdl->mod_name }}</b></td>
                                      <td>{{ $mdl->mod_desc }}</td>
                                      <td class="text-center">
                                          <div class="form-check"><input class="form-check-input prv_check"
                                                  {{ $disable_upd }} name="{{ $mdl->mod_code . '[]' }}"
                                                  id="{{ $mdl->mod_code . '_view' }}" type="checkbox" value="view">
                                          </div>
                                      </td>
                                      <td class="text-center">
                                          <div class="form-check"><input class="form-check-input prv_check"
                                                  {{ $disable_upd }} name="{{ $mdl->mod_code . '[]' }}"
                                                  id="{{ $mdl->mod_code . '_create' }}" type="checkbox" value="create">
                                          </div>
                                      </td>
                                      <td class="text-center">
                                          <div class="form-check"><input class="form-check-input prv_check"
                                                  {{ $disable_upd }} name="{{ $mdl->mod_code . '[]' }}"
                                                  id="{{ $mdl->mod_code . '_update' }}" type="checkbox" value="update">
                                          </div>
                                      </td>
                                      <td class="text-center">
                                          <div class="form-check"><input class="form-check-input prv_check"
                                                  {{ $disable_upd }} name="{{ $mdl->mod_code . '[]' }}"
                                                  id="{{ $mdl->mod_code . '_print' }}" type="checkbox" value="print">
                                          </div>
                                      </td>
                                      <td class="text-center">
                                          <div class="form-check"><input class="form-check-input prv_check"
                                                  {{ $disable_upd }} name="{{ $mdl->mod_code . '[]' }}"
                                                  id="{{ $mdl->mod_code . '_post' }}" type="checkbox" value="post"></div>
                                      </td>
                                      <td class="text-center">
                                          <div class="form-check"><input class="form-check-input prv_check"
                                              {{$disable_upd}} name="{{$mdl->mod_code . '[]'}}" id="{{$mdl->mod_code . '_delete'}}"
                                              type="checkbox" value="delete"></div>
                                      </td>
                                  </tr>
                                  @if (count($mdl->children))
                                      @include('master.role.manageChild', [
                                          'childs' => $mdl->children()->orderBy('mod_order', 'asc')->get(),
                                          'count' => 1,
                                      ])
                                  @endif
                              @endforeach
                          </tbody>
                      </table>

                      <input type="hidden" name="role_id" id="role_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    @can($menuId . '_update')
                        <button type="button" class="btn btn-primary" id="btnSave">Save</button>
                    @endcan
                </div>

                <div class="overlay" id="load-overlay">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <style>
        .btn-group {
            text-align: right;
            float: right;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 34px;
            height: 20px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 20px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 14px;
            width: 14px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #28a745;
        }
        input:checked + .slider:before {
            transform: translateX(14px);
        }
        .slider.half-active {
            background-color: #ffcc00;
        }
        .slider.half-active:after {
            content: "\f068";
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
        }
        .slider.active:after {
            content: "\f00c";
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
        }
        .slider.inactive {
            background-color: #dc3545;
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
    <script>
        let tb;
        function setDatatable() {
            tb = $('#role_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                    [3, 'desc']
                ],
                ajax: "{{ route('roles.index') }}",
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'role_name',
                        name: 'role_name'
                    },
                    {
                        data: 'role_active',
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
                    {
                        data: 'id',
                        width: '1%',
                        orderable: false,
                        render: function(data, _, row) {

                            return '<div class="btn-group">' +
                              '<button class="btn btn-primary" onclick="return privilegeHandler(' + data + ')" data-toggle="tooltip" title="Akses"><i class="fas fa-desktop"></i></button>' +
                              @can($menuId . '_update')
                                  '<a href="{{ url('roles/') }}/' + data + '/edit" class="btn btn-secondary ' + (row.role_active ? "" : "disabled") +'" data-toggle="tooltip" title="Edit"><i class="fa fa-pen"></i></a> ' +
                                  '<button onclick="return toggleState(' + data + ')" class="btn btn-' + (row.role_active ? "danger" : "primary") + '" data-toggle="tooltip" title="' + (row.role_active ? "Disable" : "Enable") + '" ><i class="fa ' + (row.role_active ? "fa-trash" : "fa-check") + '"></i></button>'
                                  // '<a href="{{ url('roles/') }}/' + data + '/toggle" class="btn btn-' + (row.role_active ? "danger" : "primary") + '" data-toggle="tooltip" title="Akses"><i class="fa ' + (row.role_active ? "fa-trash" : "fa-check") + '"></i></a>' +
                              @endcan
                            '</div>';

                            // return '<a class="btn btn-primary ' + (row.role_active ? "" : "disabled") +
                            //     '" onclick="return privilegeHandler(' + data +
                            //     ');"><i class="fas fa-desktop"></i></a> '
                            // @can($menuId . '_update')
                            //     +'<a href="{{ url('roles/') }}/' + data +
                            //         '/edit" class="btn btn-primary ' + (row.role_active ? "" : "disabled") +
                            //         '"><i class="fa fa-pen"></i></a> ' +
                            //         '<a href="{{ url('roles/') }}/' + data + '/toggle" class="btn btn-' + (
                            //             row.role_active ? "danger" : "primary") + '"><i class="fa ' + (row
                            //             .role_active ? "fa-trash" : "fa-check") + '"></i></a>';
                            // @endcan

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

        function privilegeHandler(id) {
            $('.prv_check').prop('checked', false);
            $('#load-overlay').show();
            $('#modal-privilege').modal('show');
            let url = "{{ route('roles.privileges', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'GET',
                url: url,
                success: function(res) {
                    if (res.res) {
                        $('#load-overlay').hide();
                        res.data.forEach((prv) => {
                            $('#' + prv).prop('checked', true);
                        });

                        $('#role_id').val(id);

                        $('.check-all').each(function() {
                            let target = $(this).data('target');
                            let total = $('input.prv_check[value="' + target + '"]').length;
                            let checked = $('input.prv_check[value="' + target + '"]:checked').length;

                            if (checked === total) {
                                $(this).prop('checked', true).siblings('.slider').addClass('active').removeClass('inactive half-active');
                            } else if (checked > 0) {
                                $(this).prop('checked', false).prop('indeterminate', true).siblings('.slider').addClass('half-active').removeClass('active inactive');
                            } else {
                                $(this).prop('checked', false).prop('indeterminate', false).siblings('.slider').addClass('inactive').removeClass('active half-active');
                            }
                        });
                    } else {
                        $('#role_id').val(null);
                        $('#privilege-close').trigger('click');

                        $('#modal-privilege').modal('hide');
                        toastr.error('mohon coba sesaat lagi.', 'Error');
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    $('#role_id').val(null);
                    $('#modal-privilege').modal('hide');
                    toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
                }
            });
        }

        $(function() {
          setDatatable();

          $('.check-all').on('change', function() {
            let target = $(this).data('target');
            let isChecked = $(this).prop('checked');

            // $('input[type="checkbox"][value="' + target + '"]').prop('checked', $(this).prop('checked'));
            $('input.prv_check[value="' + target + '"]').prop('checked', isChecked);
            updateToggleState($(this));
          });

          $('.prv_check').on('change', function () {
              let target = $(this).val();
              let checkAll = $('.check-all[data-target="' + target + '"]');
              let total = $('input.prv_check[value="' + target + '"]').length;
              let checked = $('input.prv_check[value="' + target + '"]:checked').length;
              
              if (checked === total) {
                  checkAll.prop({ checked: true, indeterminate: false });
                  checkAll.siblings('.slider').addClass('active').removeClass('inactive half-active');
              }
              else if (checked > 0) {
                  checkAll.prop({ checked: false, indeterminate: true });
                  checkAll.siblings('.slider').addClass('half-active').removeClass('active inactive');
              }
              else {
                  checkAll.prop({ checked: false, indeterminate: false });
                  checkAll.siblings('.slider').addClass('inactive').removeClass('active half-active');
              }

              updateToggleState(checkAll);
          });

          function updateToggleState(checkAll) {
              let switchSlider = checkAll.siblings('.slider');
              
              if (checkAll.prop('checked')) {
                  switchSlider.addClass('active').removeClass('inactive half-active');
              } else if (checkAll.prop('indeterminate')) {
                  switchSlider.addClass('half-active').removeClass('active inactive');
              } else {
                  switchSlider.addClass('inactive').removeClass('active half-active');
              }
          }

          $('input[type="checkbox"]').on('change', function() {
            $(this).closest('label').find('.slider').toggleClass('active', $(this).prop('checked'));
          });

          $('#btnSave').on('click', function() {
              let data = $('#privilege-form').serialize();
              $.ajax({
                  type: 'POST',
                  url: '{{ route('roles.prv-update') }}',
                  data: data,
                  success: function(res) {
                      if (res) {
                          $('#modal-privilege').modal('hide');
                          toastr.success('Akses telah diupdate', 'Success');
                      } else {
                          $('#modal-privilege').modal('hide');
                          toastr.error('mohon coba sesaat lagi.', 'Error');
                      }
                  },
                  error: function(xhr, ajaxOptions, thrownError) {
                      $('#modal-privilege').modal('hide');
                      toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
                  }
              });

          });
        });

        function toggleState(id) {
            let url = "{{ route('roles.toggle', ['id' => ':id']) }}";
            url = url.replace(':id', id);
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(res) {
                    if (res.res) {
                        toastr.success('Posisi berhasil diupdate', 'Success');
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
    </script>
@stop
