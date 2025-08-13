<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print PO</title>
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
            font-family:  "Lucida Console", Courier, monospace;
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
    @foreach ($purchase as $key => $value)
        <div class="header">
            <img src="{{ltrim(public_path('assets/img/RGLogo.png'), '/')}}" height="70" width="70" class="left-image">
            <h3 class="center-text">Nota Pembelian Asset</h3>
            <h6 class="right-text"><strong>{{ $value->purchase_id }}</strong></h6>
            <hr>
        </div>

        <table id="purchaseTable" style="margin-top: 20px;" border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <th style="width: 15%">PIC</th>
                <th style="width: 1%;">:</th>
                <td style="width: 30%;">{{ $value->purchase_pic == $value->user->usr_nik ? $value->user->usr_name : $value->site->si_name }}</td>
                <th style="width: 15%">Tgl. Pembelian</th>
                <th style="width: 1%;">:</th>
                <td style="width: 30%;">{{ date('d-M-Y', strtotime($value->purchase_date)) }}</td>
            </tr>
            <tr>
                <th style="width: 15%">Cabang</th>
                <th style="width: 1%;">:</th>
                <td style="width: 30%;">{{ $value->site->si_name }}</td>
                <th style="width: 15%">Lokasi</th>
                <th style="width: 1%;">:</th>
                <td style="width: 30%;">{{ $value->location->loc_name }}</td>
            </tr>
            <tr>
                <th>Notes</th>
                <th>:</th>
                <td colspan="4">{{ $value->purchase_desc }}</td>
            </tr>
        </table>
    @endforeach


        <table id="itemTable" border="1" cellspacing="0" cellpadding="0" width="100%">
            <thead>
                <tr>
                    <th>Kode Item</th>
                    <th>Nama Item</th>
                    <th>tipe</th>
                    <th>Status</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase[$key]->detail as $key => $detail)

                    <tr>
                        <td>{{ $detail->purchase_detail_id }}</td>
                        <td>{{ $detail->purchase_detail_name }}</td>
                        <td>{{ $detail->category->cat_name}}</td>
                        <td>{{ $detail->purchase_detail_status}}</td>
                        <td>Rp. {{ number_format($detail->purchase_detail_price, 2,',','.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>



        <table style="margin-top: 10px; page-break-after: auto;" width="100%;">
            <tr>
                {{-- <td align="left;">Jakarta, {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td> --}}
                <td align="right" colspan="2">Jakarta, {{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td align="left;">Dibuat Oleh,</td>
                <td align="right;">Disetujui Oleh,</td>
            </tr>
            @foreach ($purchase as $key => $item)
                <tr>
                    <td align="left;">{{ $item->detail[$key]->created_by == $item->detail[$key]->user->usr_nik ? $item->detail[$key]->user->usr_name : ''}}</td>
                    <td align="right;"></td>
                </tr>
            @endforeach
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
                <td align="right">..................................
                </td>
            </tr>
        </table>
</body>
</html>
