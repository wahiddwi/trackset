<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style type="text/css">
        @page {
            size: 1.96in 0.78in;
            margin: 0cm 0cm;
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
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(65)->generate($item->inv_transno)) !!} ">
                </td>
                <td>
                    <strong>{{ $item->inv_transno }}</strong>
                </td>
            </tr>
            <tr>
                <td>
                    {{ $item->inv_name }}
                </td>
            </tr>
    </table>

</body>
</html>
