@extends('adminlte::page')

@section('title', 'Receive')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        Receive
                        <small>
                            Asset
                            <span class="badge badge-primary">{{ $count }}</span>
                        </small>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item active">Receive</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table id="receive_table" class="table table-bordered display responsive nowrap" style="width: 100%">
            <thead>
                <tr>
                    <th>Kode tes</th>
                    <th>Cabang Asal</th>
                    <th>Lokasi Asal</th>
                    <th>Cabang Tujuan</th>
                    <th>Lokasi Tujuan</th>
                    <th>PIC Tujuan</th>
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
                <h5 class="modal-title">Detail Penerimaan Asset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped responsive">
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>Cabang Asal</strong></td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 49%; text-align: center;"><strong>Lokasi Asal</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;" id="siteFrom"></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;" id="locFrom"></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>Cabang Tujuan</strong></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;"><strong>Lokasi Tujuan</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;" id="siteTo"></td>
                        <td style="width: 2%;"></td>
                        <td style="width: 49%; text-align: center;" id="locTo"></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;"><strong>PIC Asal</strong></td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 49%; text-align: center;"><strong>PIC Tujuan</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 49%; text-align: center;" id="picFrom"></td>
                        <td style="width: 2%;">:</td>
                        <td style="width: 49%; text-align: center;" id="picTo"></td>
                    </tr>
                </table>
                <h6>Details</h6>
                <table id="detail" class="table table-striped table-bordered responsive">
                    <thead>
                        <th>#</th>
                        <th>No. Asset</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
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
    <script src="{{ asset('vendor/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $.ajax({
                type: "GET",
                url: "{{ route('receive.index') }}",
                dataType: "json",
                success: function (res) {
                    // console.log(res);
                }
            });
        });

        let tb;
        function setDatatable(){
            tb = $('#receive_table').dataTable({
                scrollX: true,
                processing: true,
                serverSide: true,
                destroy: true,
                order: [[0, 'asc']],
                ajax: "{{ route('receive.index') }}",
                columns: [
                    {data: 'trf_id', name: 'trf_id', width: '4%'},
                    {data: 'rcv.site_from', name: 'trf_site_from', className: 'dt-center', width: '2%'},
                    {data: 'rcv.loc_from', name: 'trf_loc_from', className: 'dt-center', width: '2%'},
                    {data: 'rcv.site_to', name: 'trf_site_to', className: 'dt-center', width: '2%'},
                    {data: 'rcv.loc_to', name: 'trf_loc_to', className: 'dt-center', width: '2%'},
                    {data: 'rcv.pic_to', name: 'trf_pic_to', className: 'dt-center', width: '2%'},
                    {data: 'trf_status', name: 'trf_status', className: 'dt-center', width: '2%',
                        render: function (data, type) {
                            if (type == 'display') {
                                if (data == 'TRF') {
                                    return '<span class="badge badge-primary">TRF</span>';
                                } else if (data == 'ONHAND') {
                                    return '<span class="badge badge-success">ONHAND</span>';
                                } else {
                                    return '<span class="badge badge-warning">DRAFT</span>';
                                }
                            }
                            return data;
                        }
                    },
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

                            return '<div class="btn-group">' +
                                '<button class="btn btn-primary" onclick="showModalHandler('+data+')" data-toggle="tooltip" title="Approve & View"><i class="fa-solid fa-ellipsis-vertical"></i></button>' +
                            '</div>';
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
                        url: "/receive/"+ id +"/accept",
                        dataType: "json",
                        success: function (res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Approved!',
                                text: 'Your data has been approved.',
                                showConfirmButton: false,
                            });
                            window.location.reload();
                        },
                        error: function (err) {
                            console.log(err);
                        }
                    });
                }
            });
        }

        function showModalHandler(id) {
            $('#showAssetModal').modal('show');
            $('#load-overlay').show();

            $.ajax({
                type: "GEt",
                url: "/receive/"+id,
                success: function (res) {
                    $('#load-overlay').hide();
                    let disabled = '{{ auth()->user()->can($menuId . '_update') }}' ? '' : 'disabled';

                    $('#siteFrom').text(res.site_from.si_name);
                    $('#siteTo').text(res.site_to.si_name);
                    $('#locFrom').text(res.loc_from.loc_name);
                    $('#locTo').text(res.loc_to.loc_name);
                    $('#picFrom').text(res.user == null ? res.site_from.si_name : res.user.usr_name);
                    $('#picTo').text(res.user == null ? res.site_from.si_name : res.user.usr_name);

                    var elements = '';

                    for (let i = 0; i < res.detail.length; i++) {
                        let no = (i+1);
                        let no_asset = res.detail[i].trf_detail_transno;
                        let name = res.detail[i].trf_detail_name;
                        let status = res.detail[i].trf_detail_status == 'ONHAND' ? '<span class="badge badge-success">'+ res.detail[i].trf_detail_status +'</span>' : '<span class="badge badge-warning">'+ res.detail[i].trf_detail_status +'</span>';
                        console.log(status);
                        var approveLink = "{{ url('receive') }}/"+ res.detail[i].id +"/accept";

                        elements += '<tr>';
                        elements += '<td>'+ no +'</td>';
                        elements += '<td>'+ no_asset +'</td>';
                        elements += '<td>'+ name +'</td>';
                        elements += '<td>'+ status +'</td>';
                        elements += '<td>';
                        elements += '<button class="btn btn-success" onclick="acceptRecord('+res.detail[i].id+')" data-toggle="tooltip" title="Approve" '+ (res.detail[i].trf_detail_status == "ONHAND" ? "disabled" : "") +'><i class="fa-solid fa-check"></i></button>';
                        elements += '<a href="{{ url('receive') }}/'+ res.detail[i].id + '/print" class="btn btn-primary '+ (res.detail[i].trf_detail_status == "ONHAND" ? "" : "disabled") +'" id="print" target="_blank"><i class="fas fa-print"></i></a>';
                        elements += '</td>';
                        elements += '<tr>';
                    }

                    $('#detailList').html(elements);

                }
            });

        }
    </script>
@stop
