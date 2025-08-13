<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>@yield('title')</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
    }

    main {
      margin-bottom: -15px;
    }

    .title {
      display: block;
      font-size: 1.8em;
      font-weight: bold;
    }

    .sub-title {
      display: block;
      font-size: 1.5em;
      font-weight: bold;
    }

    .site-title {
      display: block;
      font-size: 1.2em;
      font-weight: bold;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    table.border th,
    table.border td {
      border: 1px solid #000;
      padding: 4px;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .align-middle {
      vertical-align: middle;
    }

    .align-bottom {
      vertical-align: bottom;
    }

    footer {
      position: fixed;
      bottom: -60px;
      left: 0px;
      right: 0px;
      height: 50px;
    }

    .page-number:before {
      content: counter(page);
    }

    .footer-signature {
      page-break-inside: avoid;
    }
  </style>
  @yield('custom_css')
</head>

{{-- Footer paging --}}
<footer>
  <table style="width:100%">
    <tr>
      <td class="text-left"><small><span class="page-number"></span> - @yield('footer_transno')</small></td>
      <td class="text-right"><small>Raja Gadai &copy; {{ date('Y') }}</small></td>
    </tr>
  </table>
</footer>

<main>
  @yield('content')
</main>
</body>

</html>
