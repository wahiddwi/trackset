<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>RG Portal | 404 Error</title>

        @if(!config('adminlte.enabled_laravel_mix'))
            <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
            <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
            <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

            @if(config('adminlte.google_fonts.allowed', true))
                <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
            @endif
        @else
            <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
        @endif

        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />

        <style>
            section.content {
                width: 100%;
                height: 100%;
            }
            .centerPseudo {
                display: inline-block;
                text-align: center;
            }
            .centerPseudo::before {
                content: '';
                display: inline-block;
                height: 100%;
                vertical-align: middle;
                width: 0px;
            }
            .error-options{
                padding: 20px;
            }

        </style>
    </head>

    <body>    
        <div class="error-options">
            <h3><i class="fa fa-chevron-circle-left text-muted"></i> <a href="javascript:history.back()">Go Back</a></h3>
        </div>
        <section class="content centerPseudo">
            <div class="error-page centerPseudo">
                <h2 class="headline text-warning"> 404</h2>
        
                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>    
                    <p>
                        Oops, we are sorry but the page you are looking for was not found..
                    </p>
                </div>
            </div>
        </section>
    </body>
</html>
