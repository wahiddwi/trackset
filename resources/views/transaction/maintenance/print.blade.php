<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $maintenance->main_transno }}</title>
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
            /* font-size: 12pt; */
            font-size: 75%;
        }

        table.table th {
          font-size: 12pt;
          border-top: 1px solid #7b7b7b;
          border-bottom: 1px solid #7b7b7b;
        }

        td {
          padding: 0;
          margin: 0;
          font-size: 10pt;
        }

        footer {
          position: fixed;
          bottom: -32px;
          left: 1cm;
          right: 1cm;
          height: 50px;
        }

        .footer {
          page-break-inside: avoid;
        }

        .page-number:before {
          content: counter(page);
        }

        table.content td {
          text-align: center;
        }
    </style>
</head>
<body>
  <table style="width:100%; border-collapse: collapse;">
    <tr>
        <td class="text-left" colspan="3">
            <h4>{{ $maintenance->main_transno }}</h4>
        </td>
        <td class="text-right" style="width: 30%">
            <h5 style="text-align: right; margin-bottom: 0; line-height: 1;">MAINTENANCE ASSET</h5>
            <h6 style="text-align: right; line-height: 1; margin-top: 2px;" >{{ strtoupper($maintenance->company->co_name) }}</h6>
        </td>
    </tr>
    <tr>
        <td class="text-left" style="width: 150px">Teknisi</td>
        <td class="text-left" style="width: 10px">:</td>
        <td class="text-left">{{ $maintenance->main_tech_name }}</td>
        <td class="text-right" style="font-size: 10pt; text-align: right;">
          {{ \Carbon\Carbon::parse($maintenance->main_transdate)->format('d M Y') }}
      </td>
    </tr>
    <tr>
        <td class="text-left align-top">No. Telp</td>
        <td class="text-left align-top">:</td>
        <td class="text-left align-top">{{ $maintenance->main_tech_contact }}</td>
    </tr>
  </table>

  <br />

  <table class="table table-sm content" style="width: 100%; border-collapse: collapse;">
    <thead>
      <tr>
        <th class="text-center align-middle">#</th>
        <th>No. Asset</th>
        <th>Asset</th>
        <th>Keterangan</th>
        <th>Service</th>
        <th>Biaya</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($maintenance->detail as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $item->maindtl_asset_transno }}</td>
          <td>{{ $item->maindtl_asset_name }}</td>
          <td>{{ $item->maindtl_desc ? $item->maindtl_desc : '' }}</td>
          <td>{{ $item->maindtl_counter }}</td>
          <td>{{ 'Rp. ' . number_format($item->maindtl_cost, 0, ',', '.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <br />
  <div class="footer" style="width:100%">
    <table>
      <tr>
        <td>Dibuat Oleh</td>
        <td class="pl-2">:</td>
        <td class="pl-2">{{ $maintenance->created_by_name.' ('.$maintenance->created_by.')' }}</td>
      </tr>
      <tr>
        <td>Disetujui Oleh</td>
        <td class="pl-2">:</td>
        <td class="pl-2">{{ $maintenance->approver_by_name.' ('.$maintenance->approver_by.')' }}</td>
      </tr>
    </table>
    <br>
    <small>* INI ADALAH CETAKAN KOMPUTER, SEHINGGA TANDATANGAN TIDAK DIPERLUKAN</small><br>
    <small>Dicetak pada: {{ date('d M Y H:i:s') }}</small>
  </div>
  <footer>
    <table style="width:100%; border-collapse: collapse;">
      <tr>
        <td class="text-left" style="font-size: 10pt;"><small><span class="page-number"></span> - {{ $maintenance->main_transno }}</small></td>
        <td class="text-right" style="text-align: right; font-size: 10pt;"><small>Raja Gadai &copy; {{ date('Y') }}</small></td>
      </tr>
    </table>
  </footer>
</body>
</html>
