<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>QR - {{ $asset->inv_transno }}</title>
    <style type="text/css">
        @page {
            size: 5cm 2cm;
            margin: 0;
        }
        body {
            margin-top:1mm;
            margin-right: 1mm;
            margin-bottom: 0.5mm;
            margin-left: 1mm;
            color: black;
        }
        table tr td {
            font-size: 10pt;
            color: black;
        }
    </style>
</head>
<body>
    <table style="text-align: center; border: none;" border="0" cellspacing="0" cellpadding="0" width="100%;">
            <tr>
                <td rowspan="2">
                    <img src="data:image/svg+xml;base64, {!! base64_encode(QrCode::format('svg')
                            ->size(65)
                            ->margin(1)
                            ->errorCorrection('M')
                            ->generate($asset->inv_transno)) !!} ">
                    <!-- <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(65)->generate($asset->inv_transno)) !!} "> -->
                </td>
                <td>
                    <strong>{{ $asset->inv_transno }}</strong>
                </td>
            </tr>
            <tr>
                <td>
                    {{ substr($asset->inv_name_short, 0, 24) }}
                </td>
            </tr>
    </table>

</body>
</html>
