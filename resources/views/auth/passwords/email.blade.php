<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $site_setting->site_name }}</title>
	
    
    <link rel="shortcut icon" href="{{ asset('/') }}images/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="{{ asset('/') }}images/favicon.ico" type="image/x-icon" />
    <link rel="icon" type="image/png" href="{{ asset('/') }}images/favicon.png" />
    
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('css/skins/skin-purple.min.css') }}">
</head>
<body class="hold-transition skin-purple login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('admin') }}"><b>{{ $site_setting->site_name }}</b></a>
        </div>
        <!-- /.login-logo -->
        @include('layouts.errors-and-messages')
        <div class="login-box-body">
            <p class="login-box-msg">Forgot Password</p>

            <form action="{{ route('admin.forgotPasswordSend') }}" method="post">
                {{ csrf_field() }}
                <div class="form-group has-feedback">
                    <input name="email" type="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @if( $errors->has('email') )
                        <span class="text-danger fname">
                                {{ $errors->first('email') }}
                        </span>
                    @endif

                </div>

                <div class="row">
                    <div class="col-xs-8">
                        <a href="{{ route('admin.login') }}">Back to Sign in</a><br>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            {{-- <div class="social-auth-links text-center">
                <p>- OR -</p>
                <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
                    Facebook</a>
                <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
                    Google+</a>
            </div> --}}
            <!-- /.social-auth-links -->

            {{-- <a href="{{ url('/') }}" class="text-center">Register a new membership</a> --}}

        </div>
        <!-- /.login-box-body -->
    </div>
    <!-- /.login-box -->
    <!-- jQuery 2.2.3 -->
    <script src="{{ asset('js/jquery-2.2.3.min.js') }}"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('js/fastclick.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('js/app.min.js') }}"></script>
</body>
</html>