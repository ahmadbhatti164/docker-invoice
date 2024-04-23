<header class="">
    <!-- Logo -->
    <a href="#" class="logo"></a>

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <!-- Actual search box -->
        <div class="has-search search-box">
            <span class="fa fa-search form-control-feedback"></span>
            <input type="text" class="form-control" placeholder="Search" autocomplete="off">
            <div class="result"></div>
        </div>
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
                            {{-- <img src="{{ asset('img/user2-160x160.jpg') }}" class="user-image" alt="User Image"> --}}
                            <span class="hidden-xs">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="{{ route('editProfile', auth()->user()->id) }}" class="btn btn-default btn-flat sign-out">
                                        <i class="fa fa-user"></i>
                                        Edit Profile
                                    </a>
                                    <a  class="btn btn-default btn-flat sign-out" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa fa-fw fa-power-off"></i>
                                        {{ __('Logout') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </ul>
    </nav>
</header>
