<!DOCTYPE html>
<html lang="en-US" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <!-- ===============================================-->
        <!--    Document Title-->
        <!-- ===============================================-->
        <title>Ruang Anagata | Learning Management System </title>


        <!-- ===============================================-->
        <!--    Favicons-->
        <!-- ===============================================-->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('asset/img/favicons/favicon.ico') }}">
        <link rel="icon" type="image/x-icon" sizes="32x32" href="{{ asset('asset/img/favicons/favicon.ico') }}">
        <link rel="icon" type="image/x-icon" sizes="16x16" href="{{ asset('asset/img/favicons/favicon.ico') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('asset/img/favicons/favicon.ico') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
        <link rel="manifest" href="{{ asset('asset/img/favicons/manifest.json') }}">
        <meta name="msapplication-TileImage" content="{{ asset('asset/img/favicons/favicon.ico') }}">
        <meta name="theme-color" content="#ffffff">


        <!-- ===============================================-->
        <!--    Stylesheets-->
        <!-- ===============================================-->
        <link href="{{ asset('asset/css/theme.css') }}" rel="stylesheet" />

    </head>



    <body>

        <!-- Navbar -->
        @include('layouts.navbar')
        <!-- / Navbar -->



        <!-- ===============================================-->
        <!--    Main Content-->
        <!-- ===============================================-->
        @yield('content')
        <!-- ===============================================-->
        <!--    End of Main Content-->
        <!-- ===============================================-->




        <!-- ===============================================-->
        <!--    JavaScripts-->
        <!-- ===============================================-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.8/umd/popper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/is_js@0.9.0/is.min.js"></script>
        <script>
            if (!('scrollBehavior' in document.documentElement.style)) {
                // Load a smooth scroll polyfill dynamically
                var script = document.createElement("script");
                script.src = "https://cdnjs.cloudflare.com/ajax/libs/smoothscroll-polyfill/0.4.4/smoothscroll.min.js";
                document.head.appendChild(script);
            }
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
        <script src="{{ asset('asset/js/theme.js') }}"></script>

        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;family=Volkhov:wght@700&amp;display=swap"
            rel="stylesheet">
    </body>

</html>
