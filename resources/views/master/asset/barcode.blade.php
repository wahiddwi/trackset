<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>QR - {{ $asset->inv_transno }}</title>
    <style type="text/css">
        @page {
            size: 1.96in 0.78in;
            /* margin: 0cm 0cm; */
            margin: 0;
        }
        body {
            margin-top:2mm;
            margin-right: 1mm;
            margin-bottom: 0.5mm;
            margin-left: 1mm;
            color: black;
        }
        table tr td {
            font-size: 10pt;
            color: black;
        }
        .code td {
            padding-top: 3px;
            font-size: 8pt;
        }
        .name td {
            font-size: 9pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <table style="text-align: center; border: none;" border="0" cellspacing="0" cellpadding="0" width="100%;">
      <tr class="barcode">
        <td>
          <img src="data:image/svg+xml;base64,{{ base64_encode($barcodeSVG) }}" />
        </td>
      </tr>
      <tr class="code">
        <td>{{ $asset->inv_transno }}</td>
      </tr>
      <tr class="name">
        <td>{{ substr($asset->inv_name_short, 0, 24) }}</td>
      </tr>
    </table>

</body>
</html>
