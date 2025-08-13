<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head>
<body>
    <table>
        <thead>
            <tr>                                                    
                <th>Kode Asset</th>
                <th>Nama</th>
                <th>Tgl. Perolehan</th>
                <th>Cabang</th>
                <th>Lokasi</th>
                <th>Kategori</th>
                <th>PIC</th>
                <th>Status</th>
            </tr>
        </thead>              
        @foreach($reports as $report)
            <tr>
                <td>{{ $report->inv_transno }}</td>
                <td>{{ $report->inv_name }}</td>
                <td>{{ date('d-m-Y', strtotime($report->inv_obtaindate)) }}</td>
                <td>{{ $report->inv_site .' - '. $report->site->si_name }}</td>
                <td>{{ '('.$report->location->loc_id.') - '.$report->location->loc_name }}</td>
                <td>{{ $report->category->cat_code.' - '.$report->category->cat_name }}</td>
                <td>{{ $report->inv_pic_type == 'cabang' ? $report->inv_site .' - '. $report->site->si_name : $report->inv_pic .' - '. $report->pic->pic_name }}</td>
                <td>{{ $report->inv_status }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
