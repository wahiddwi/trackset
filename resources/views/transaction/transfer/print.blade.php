<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $data->trf_transno }}</title>
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
            font-size: 12pt;
        }

        table.table th {
          /* font-size: 1.2rem; */
          font-size: 12pt;
          border-top: 1px solid #7b7b7b;
          border-bottom: 1px solid #7b7b7b;
          /* padding: 0.5rem 0.5rem;
          vertical-align: middle; */
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
            <h4>{{ $data->trf_transno }}</h4>
        </td>
        <td class="text-right" style="width: 30%">
            <h5 style="text-align: right; margin-bottom: 0; line-height: 1;">TRANSFER ASSET</h5>
            <h6 style="text-align: right; line-height: 1; margin-top: 2px;" >{{ strtoupper($data->company->co_name) }}</h6>
        </td>
    </tr>
    <tr>
        <td class="text-left" style="width: 150px">Cabang</td>
        <td class="text-left" style="width: 10px">:</td>
        <td class="text-left">{{ $data->trf_site_from . ' - ' . $data->siteFrom->si_name }}</td>
        <td class="text-right" style="font-size: 10pt; text-align: right;">{{ $data->trf_transdate->format('d M Y') }}</td>
    </tr>
    <tr>
        <td class="text-left align-top">Gudang</td>
        <td class="text-left align-top">:</td>
        <td class="text-left align-top">{{ $data->locFrom->loc_name . ' (' . $data->locFrom->loc_id . ')' }}</td>
    </tr>
    <tr style="height: 10px">
        <td class="text-left align-top">Keterangan</td>
        <td class="text-left align-top">:</td>
        <td class="text-left align-top">{{ $data->trf_desc ? $data->trf_desc : '' }}</td>
    </tr>
  </table>

  <br />

  <table class="table table-sm content" style="width: 100%; border-collapse: collapse;">
    <thead>
      <tr>
        <th class="text-center align-middle">#</th>
        <th>Cabang</th>
        <th>Lokasi</th>
        <th>Asset</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      {{-- <tr>
        <td>1</td>
        <td>001 - Outlet Merdeka</td>
        <td>001-001 - Outlet Merdeka</td>
        <td>Kursi Kerja</td>
        <td></td>
      </tr>
      <tr>
        <td>2</td>
        <td>001 - Outlet Merdeka</td>
        <td>001-001 - Outlet Merdeka</td>
        <td>Meja Kerja</td>
        <td></td>
      </tr> --}}
      @foreach ($data->detail as $item)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $data->siteTo->si_site . ' - ' . $data->siteTo->si_name }}</td>
          <td>{{ $data->locTo->loc_name . ' (' . $data->locTo->loc_id . ')' }}</td>
          <td>{{ $item->trfdtl_name }}</td>
          <td>{{ $item->trfdtl_desc }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <br />
  <div class="footer" style="width:100%">
    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <td class="text-center">Membuat,</td>
            <td style="width: 70%"></td>
            <td class="text-center">Menyetujui,</td>
        </tr>
        <tr>
            <td class="text-center align-bottom" style="vertical-align: bottom;">
                {{-- {{ $data->del_maker ? $data->del_maker : '..................................' }}</td> --}}
                Wahid Dwi Saputra
            </td>
            <td style="width: 70%" style="height: 120px"></td>
            <td class="text-center align-bottom" style="vertical-align: bottom;">
                {{-- {{ $data->del_approver ? $data->del_approver : '..................................' }}</td> --}}
                Ario Nugroho
            </td>
        </tr>
        <tr>
            <td class="text-center">{{ date('d M Y') }}</td>
            <td style="width: 70%"></td>
            <td class="text-center"></td>
        </tr>
    </table>
</div>
  <footer>
    <table style="width:100%; border-collapse: collapse;">
      <tr>
        <td class="text-left" style="font-size: 10pt;"><small><span class="page-number"></span> - {{ $data->trf_transno }}</small></td>
        <td class="text-right" style="text-align: right; font-size: 10pt;"><small>Raja Gadai &copy; {{ date('Y') }}</small></td>
      </tr>
    </table>
  </footer>
</body>
</html>
