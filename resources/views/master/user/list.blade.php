@extends('adminlte::page')

@section('title', 'Master User')

@section('content_header')
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">
            Master Data
            <small>
              Users
              <span class="badge badge-primary">{{ $count }}</span>
            </small>
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Master Users</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
@stop

@section('content')
  <div class="card">
    <div class="card-body">
      @can(session('menuId') . '_create')
        <a href="{{ route('users.create') }}" class="btn btn-primary">Add</a><br /><br />
      @endcan
      <table id="user_table" class="table table-bordered display responsive nowrap" style="width: 100%">
        <thead>
          <tr>
            <th>NIK</th>
            <th>Nama</th>
            <th>Posisi</th>
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


  <!-- Modal -->
  <div class="modal fade" id="modal-privilege" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Site Access</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="privilege-close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="" id="privilege-form">
            @csrf
            <table class="table table-hover table-sm diplay responsive nowrap" style="width:100%" id="sites_table">
              <thead>
                <tr>
                  <th>Outlet</th>
                  <th class="text-center" style="width: 5vw">Access</th>
                  <th class="text-center" style="width: 5vw">Default</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $disable_upd = auth()
                      ->user()
                      ->can(session('menuId') . '_update')
                      ? ''
                      : ' disabled';
                @endphp
                @foreach ($groupSites as $company => $sites)
                  <tr class="toggle-group" data-group="{{ $company }}" role="button">
                    <td>
                      <b><i class="fa fa-fw fa-angles-right"></i> {{ strtoupper($sites->first()->company->co_name) }}</b>
                    </td>
                    <td class="text-center align-middle" colspan="2">
                      {{-- <div class="form-check">
                        <input class="form-check-input group_check group-{{ $company }}" {{ $disable_upd }}
                          type="checkbox" value="{{ $company }}">
                      </div> --}}

                      <label class="switch">
                        <input type="checkbox" value="{{ $company }}" class="group_check group-{{ $company }}">
                        <span class="slider"></span>
                      </label>

                      {{-- <div class="custom-control custom-switch w-100">
                        <input type="checkbox" class="custom-control-input group_check group-{{ $company }}"
                          id="switch-{{ $company }}">
                        <label class="custom-control-label" for="switch-{{ $company }}"></label>
                      </div> --}}

                    </td>
                    {{-- <td></td> --}}
                  </tr>
                  @foreach ($sites as $site)
                    <tr class="group-rows group-{{ $company }}">
                      <td>{{ $site->si_site . ' - ' . $site->si_name }}</td>
                      <td class="text-center">
                        <div class="form-check"><input class="form-check-input site_check group-{{ $company }}"
                            data-group="{{ $company }}" {{ $disable_upd }} name="siteAccess[]" type="checkbox"
                            value="{{ $site->si_site }}"></div>
                      </td>
                      <td class="text-center">
                        <div class="form-check"><input class="form-check-input default_check" {{ $disable_upd }}
                            name="default[]" type="radio" value="{{ $site->si_site }}"></div>
                      </td>
                    </tr>
                  @endforeach
                @endforeach
              </tbody>
            </table>

            <input type="hidden" name="usr_id" id="usr_id">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          @can(session('menuId') . '_update')
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
@section('plugins.Datatables', true)
@section('css')
  <style>
    /* The switch - the box around the slider */
    .switch {
      font-size: 10px;
      position: relative;
      display: inline-block;
      width: 3.5em;
      height: 1.7em;
    }

    /* Hide default HTML checkbox */
    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    /* The slider */
    .slider {
      position: absolute;
      cursor: pointer;
      inset: 0;
      background: rgb(0, 0, 0, 0.38);
      border-radius: 50px;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .slider:before {
      position: absolute;
      content: "";
      display: flex;
      align-items: center;
      justify-content: center;
      height: 1.7em;
      width: 1.7em;
      inset: 0;
      background-color: white;
      border-radius: 50px;
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .switch input:indeterminate+.slider {
      background: var(--secondary);
    }

    .switch input:checked+.slider {
      background: var(--primary);
    }

    .switch input:focus+.slider {
      box-shadow: 0 0 1px var(--primary);
    }

    .switch input:indeterminate+.slider:before {
      transform: translateX(1em);
      content: "~";
    }

    .switch input:checked+.slider:before {
      transform: translateX(2em);
      content: "✓";
    }
  </style>
@stop

@section('js')
  <script src="{{ asset('vendor/moment-js/moment.min.js') }}"></script>
  <script>
    let tb;

    function setDatatable() {
      tb = $('#user_table').dataTable({
        scrollX: true,
        processing: true,
        serverSide: true,
        destroy: true,
        order: [
          [4, 'desc']
        ],
        ajax: "{{ route('users.index') }}",
        columns: [{
            data: 'usr_nik',
            name: 'usr_nik'
          },
          {
            data: 'usr_name',
            name: 'usr_name'
          },
          {
            data: 'role.role_name',
            name: 'role.role_name'
          },
          {
            data: 'usr_status',
            name: 'usr_status',
            className: 'dt-center',
            width: '7%',
            render: function(data, type, row) {
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
            data: 'usr_id',
            width: '1%',
            orderable: false,
            filterable: false,
            searchable: false,
            render: function(data, _, row) {
              let disabled = '{{ auth()->user()->can(session('menuId') . '_update') }}' ? '' : 'd-none';

              return '<div class="btn-group">' +
                '<a class="btn btn-primary" onclick="return privilegeHandler(' + data +
                ');" data-toggle="tooltip" title="Akses Cabang"><i class="fa fa-tasks"></i></a>' +
                '<a href="{{ url('users/') }}/' + data +
                '/edit" class="btn btn-secondary ' + disabled +
                '" data-toggle="tooltip" title="View"><i class="fa fa-pen"></i></a>' +
                // '<a href="{{ url('users/') }}/' + data + '/delete" class="btn btn-danger" ' + disabled + '" data-toggle="tooltip" title="View"><i class="fa fa-trash"></i></a>'+
                '<button onclick="return toggleState(' + data + ');" class="btn btn-' + (row
                  .usr_status ? "danger" : "primary") + disabled +
                '" data-toggle="tooltip" title="' + (row.usr_status ? "Disable" :
                  "Enable") + '"><i class="fa ' + (row.usr_status ? "fa-trash" :
                  "fa-check") +
                '"></i></button>'
              '</div>';
            }
          },
        ]
      });
    }

    function privilegeHandler(id) {
      $('.site_check').prop('checked', false);
      $('.group_check').prop('checked', false).prop('indeterminate', false);
      $('.default_check').prop('checked', false);
      $('#load-overlay').show();
      $('#modal-privilege').modal('show');

      // close all company
      $('.group-rows').addClass('d-none');
      $('.toggle-group').removeClass('is-open');
      $('.toggle-group i').attr('class', 'fa fa-fw fa-angles-right');

      $.ajax({
        type: 'GET',
        url: '{{ url('/users/privileges') }}' + '/' + id,
        success: function(res) {
          if (res.res) {
            $('#load-overlay').hide();
            res.data.forEach((site) => {
              $('input.site_check[value="' + site.su_site + '"]').prop('checked', true);
              if (site.su_default) $('input.default_check[value="' + site.su_site + '"]')
                .prop('checked', true);
            });

            Object.entries(res.company).forEach(([company, state]) => {
              if (state) $(`.group_check.group-${company}`).prop('checked', true);
              else $(`.group_check.group-${company}`).prop('indeterminate', true);
            });

            $('#usr_id').val(id);
          } else {
            $('#usr_id').val(null);
            $('#privilege-close').trigger('click');

            $('#modal-privilege').modal('hide');
            toastr.error('mohon coba sesaat lagi.', 'Error');
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          $('#user_id').val(null);
          $('#modal-privilege').modal('hide');
          toastr.error('mohon coba sesaat lagi. (' + xhr.status + ')', 'Error');
        }
      });
    }

    $(function() {
      setDatatable();
      $('.group-rows').addClass('d-none');
      $('#sites_table .toggle-group').on('click', function(e) {
        // if ($(e.target).is('.form-check-input')) return
        if ($(e.target).is('td.switch')) return
        const company = $(this).data('group');
        $(this).toggleClass('is-open');
        $(`.group-rows.group-${company}`).toggleClass('d-none');

        if ($(this).hasClass('is-open')) {
          $(this).find('i').attr('class', 'fa fa-fw fa-angles-down');
        } else {
          $(this).find('i').attr('class', 'fa fa-fw fa-angles-right');
        }
      });

      $('.group_check').on('click', function() {
        const company = $(this).val();
        const flag = $(this).prop('checked');

        $(`.group-rows.group-${company} .site_check`).prop('checked', flag);
      });

      $('.site_check').on('click', function() {
        const flag = $(this).prop('checked');
        const company = $(this).data('group');

        const count = $(`.site_check.group-${company}:checked`).length;
        const total = $(`.site_check.group-${company}`).length;

        if (flag) {
          if (count == total) {
            $(`.group_check.group-${company}`).prop('indeterminate', false).prop('checked', true);
          } else {
            $(`.group_check.group-${company}`).prop('indeterminate', true);
          }
        } else {
          if (count == 0) {
            $(`.group_check.group-${company}`).prop('indeterminate', false).prop('checked', false);
          } else {
            $(`.group_check.group-${company}`).prop('indeterminate', true);
          }
        }
      });

      $('#btnSave').on('click', function() {
        if ($('.site_check:checked').length < 1) {
          toastr.error('mohon lengkapi access outlet.', 'Error');
          return;
        }
        if ($('.default_check:checked').length < 1) {
          toastr.error('mohon pilih outlet default.', 'Error');
          return;
        }

        let siteList = [];
        $("input.site_check:checked").each(function() {
          siteList.push($(this).val());
        });
        if (!siteList.includes($('.default_check:checked').val())) {
          toastr.error('Outlet default tidak terpilih.', 'Error');
          return;
        }

        let data = $('#privilege-form').serialize();
        $.ajax({
          type: 'POST',
          url: '{{ route('users.prv-update') }}',
          data: data,
          success: function(res) {
            if (res == 'success') {
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
      let url = "{{ route('users.toggle', ['id' => ':id']) }}";
      url = url.replace(':id', id);
      $.ajax({
        type: 'POST',
        url: url,
        data: {
          "_token": "{{ csrf_token() }}",
        },
        success: function(res) {
          if (res.res) {
            toastr.success('User berhasil diupdate', 'Success');
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
