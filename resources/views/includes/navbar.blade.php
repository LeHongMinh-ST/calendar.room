<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        @auth()
            <li class="dropdown">
            <a href="#" data-toggle="dropdown"
               class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{asset('/')}}assets/img/avatar/avatar-1.png" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">Xin chào, {{ Auth::user()->full_name}}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('profile.index') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Thông tin cá nhân
                </a>

                <a href="{{ route('profile.change-password') }}" class="dropdown-item has-icon">
                    <i class="fas fa-lock"></i> Đổi mật khẩu
                </a>
                @if(Auth::user()->role_id == 1)
{{--                <a href="features-settings.html" class="dropdown-item has-icon">--}}
{{--                    <i class="fas fa-cog"></i> Cài đặt--}}
{{--                </a>--}}
                @endif
                <div class="dropdown-divider"></div>
                <a href="{{route('logout')}}" class="dropdown-item has-icon text-danger">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </li>
        @else
            <li class="dropdown dropdown-list-toggle">
                <a href="{{route('login')}}"class="nav-link nav-link-lg">
                    <i class="fas fa-sign-in-alt"></i>
                    <div class="d-sm-none d-lg-inline-block">Đăng nhập</div>
                </a>
            </li>
        @endauth
    </ul>
</nav>
