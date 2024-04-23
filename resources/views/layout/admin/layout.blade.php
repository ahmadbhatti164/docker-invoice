<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- FAVICON-->
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">
        <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/favicon.png') }}">
        <link rel="msapplication-TileImage" href="{{ asset('assets/images/favicon.png') }}">

        <title>Admin | Invoice System</title>

        <meta content="{{ csrf_token() }}" name="csrf-token" />

        @include('include.admin.css')

        @stack('css')
    </head>

    <body class="hold-transition skin-white sidebar-mini"> <!-- skin-purple -->

        <!-- Site wrapper -->
        <div class="wrapper">

        @include('layout.admin.header')

        @include('layout.admin.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- /.content-wrapper -->

        @include('layout.admin.footer')

        </div>


        @include('include.admin.js')

        <script type="text/javascript">
            var APP_URL = {!! json_encode(url('/')) !!}
            $(document).ready(function(){

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-Token': '{{ csrf_token() }}'
                    }
                });
            });
        </script>

        @stack('script')

    </body>
</html>
