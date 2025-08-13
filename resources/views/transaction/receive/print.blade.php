<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>RCV - {{ $detail->transfer->trf_id }}</title>
    <style type="text/css">
        @page {
            size: 21cm 14cm;
            margin: 0cm 0cm;
        }
        body {
            margin-top:0.5cm;
            margin-right: 1cm;
            margin-bottom: 0.5cm;
            margin-left: 1cm;
            color: black;
        }

        .header {
            text-align: center;
            position: relative;
        }

        .left-image {
            position: absolute;
            left: 0;
        }

        .right-text {
            position: absolute;
            right: 0;
            max-width: 100%;
            height: auto;
            width: auto;
            font-size: 10pt;
            font-family: "Lucida Console", Courier, monospace;
        }

        .center-text {
            display: inline-block;
            max-width: 100%;
            height: auto;
        }

        #itemTable {
            margin-top: 20px;
            text-align: center;
        }

        #purchaseTable th {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ltrim(public_path('assets/img/RGLogo.png'), '/')}}" height="70" width="70" class="left-image">
        <h3 class="center-text">Nota Transfer Asset</h3>
        <h6 class="right-text"><strong>{{ $detail->transfer->trf_id }}</strong></h6>
        <hr>
    </div>

    {{-- purchase --}}
    <table id="purchaseTable" style="margin-top: 20px;" border="0" cellspacing="0" cellpadding="0" width="100%">
        <tr>
            <th style="width: 15%">Cabang Asal</th>
            <th style="width: 1%;"></th>
    {{-- @dd($detail) --}}
            <td style="width: 30%;">{{ $detail->transfer->siteFrom->si_name }}</td>
            <th style="width: 15%">Lokasi Asal</th>
            <th style="width: 1%;"></th>
            <td style="width: 30%;">{{ $detail->transfer->locFrom->loc_name }}</td>
        </tr>
        <tr>
            <th style="width: 15%">Cabang Tujuan</th>
            <th style="width: 1%;"></th>
            <td style="width: 30%;">{{ $detail->transfer->siteTo->si_name }}</td>
            <th style="width: 15%">Lokasi Tujuan</th>
            <th style="width: 1%;"></th>
            <td style="width: 30%;">{{ $detail->transfer->locTo->loc_name }}</td>
        </tr>
        <tr>
            <th style="width: 15%">PIC Asal</th>
            <th style="width: 1%;"></th>
            <td style="width: 30%;">{{ $detail->transfer->userFrom != null && $detail->transfer->trf_pic_from == $detail->transfer->userFrom->usr_nik ? $detail->transfer->userFrom->usr_name : $detail->transfer->siteFrom->si_name }}</td>
            <th style="width: 15%">PIC Tujuan</th>
            <th style="width: 1%;"></th>
            <td style="width: 30%;">{{$detail->transfer->userTo != null && $detail->transfer->trf_pic_to == $detail->transfer->userTo->usr_nik ? $detail->transfer->userTo->usr_name : $detail->transfer->siteTo->si_name }}</td>
        </tr>
    </table>


    <table id="itemTable" border="1" cellspacing="0" cellpadding="0" width="100%" style="page-break-after: auto;">
        <thead>
            <tr>
                <th>No. Item</th>
                <th>Item Name</th>
                <th>Status</th>
                <th>Received By</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $detail->trf_detail_transno }}</td>
                <td>{{ $detail->trf_detail_name }}</td>
                <td>{{ $detail->trf_detail_status }}</td>
                <td>{{ $detail->received->usr_name }}</td>
            </tr>
            {{-- @foreach ($transfer->detail as $key => $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->trf_detail_transno }}</td>
                    <td>{{ $item->trf_detail_name }}</td>
                    <td>{{ $item->trf_detail_status }}</td>
                </tr>
            @endforeach --}}
        </tbody>
    </table>

    <table style="margin-top: 10px; page-break-after: auto;" width="100%;">
        <tr>
            {{-- <td align="left;">Jakarta, {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td> --}}
            <td align="right" colspan="3">Jakarta, {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td align="left;">Dibuat Oleh,</td>
            <td align="center;">Disetujui Oleh,</td>
            <td align="right;">Diterima Oleh,</td>
        </tr>
        <tr>
            <td align="left;">{{ $detail->createdBy->usr_name }}</td>
            <td align="center;"></td>
            <td align="right;">{{ $detail->received->usr_name }}</td>
        </tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr><td></td><td></td></tr>
        <tr>
            <td align="left;">..................................
            </td>
            <td align="center;">..................................
            </td>
            <td align="right">..................................
            </td>
        </tr>
    </table>

</body>
</html>
