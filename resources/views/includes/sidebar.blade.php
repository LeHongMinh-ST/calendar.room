<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        @auth()
            <div class="sidebar-brand">
                <a href="@if(Auth::user()->role_id == 1){{route('backend.dashboard')}}@else{{route('calendar')}} @endif">
                    <img src="{{asset('/')}}assets/img/FITA.png" alt="logo" style="max-block-size: 100%; margin-right: 10px;">FITA-VNUA
                </a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="@if(Auth::user()->role_id == 1){{route('backend.dashboard')}}@else{{route('calendar')}} @endif">
                    <img src="{{asset('/')}}assets/img/FITA.png" alt="logo" style="max-block-size: 100%;">
                </a>
            </div>
        @else
            <div class="sidebar-brand">
                <a href="{{route('calendar')}}">
                    <img src="{{asset('/')}}assets/img/FITA.png" alt="logo" style="max-block-size: 100%;">FITA-VUNA</a>
            </div>
            <div class="sidebar-brand sidebar-brand-sm">
                <a href="{{route('calendar')}}">
                    <img src="{{asset('/')}}assets/img/FITA.png" alt="logo" style="max-block-size: 100%;">
                </a>
            </div>
        @endauth
        <ul class="sidebar-menu">
            @can('view-admin')
                <li class="menu-header active">Bản điều khiển</li>
                <li class="nav-item {{ request()->is('admin') || request()->is('admin/dashboard') ? 'active' : null}}">
                    <a href="{{route('backend.dashboard')}}" class="nav-link"><i
                            class="fas fa-fire"></i><span>Bảng điều khiển</span></a>
                </li>
                <li class="menu-header">{{__('General')}}</li>
                <li class="nav-item dropdown {{request()->is('admin/faculty') ? 'active' : null}}">
                    <a href="{{route('backend.faculty.index')}}" class="nav-link"><i class="fas fa-building"></i>
                        <span>Quản lý khoa</span></a>
                </li>
                <li class="nav-item dropdown {{request()->is('admin/department') ? 'active' : null}}">
                    <a href="{{route('department.index')}}" class="nav-link"><i class="fas fa-columns"></i>
                        <span>Quản lý bộ môn</span></a>
                </li>
                <li class="nav-item dropdown {{request()->is('admin/semester') ? 'active' : null}}">
                    <a href="{{route('backend.semester.index')}}" class="nav-link"><i class="fas fa-calendar-week"></i>
                        <span>Quản lý Học kì-Tuần</span></a>
                <li class="nav-item dropdown {{request()->is('admin/subject') ? 'active' : null}}">
                    <a href="{{route('subject.index')}}" class="nav-link" ><i class="fas fa-tasks"></i>
                        <span>Quản lý môn học</span>
                    </a>
                </li>
                <li class="nav-item dropdown {{request()->is('admin/room') ? 'active' : null}}">
                    <a href="{{route('backend.room.index')}}" class="nav-link"><i class="fas fa-server"></i>
                        <span>Quản lý phòng máy</span></a>
                </li>
            @endcan
            <li class="menu-header">Thời khóa biểu</li>
            <li class="nav-item {{ request()->is('calendar') ? 'active' : null}}">
                <a href="{{route('calendar')}}" class="nav-link"><i class="fas fa-calendar-alt"></i><span>Thời khóa biểu</span></a>
            </li>
            @can('view-admin')
                <li class="nav-item {{request()->is('admin/schedules') ? 'active' : null}}">
                    <a href="{{ route('schedules.index') }}" class="nav-link"><i class="fa fa-check" aria-hidden="true"></i>
                        <span>Xác nhận đăng kí</span></a>
                </li>
            @endcan
            @can('view-auth')
                <li class="nav-item {{ request()->is('calendar/register') ? 'active' : null}}">
                    <a href="{{route('calendar.register')}}" class="nav-link"><i class="fas fa-calendar-plus"></i><span>Đăng kí TKB</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->is('calendar/register-schedules') ? 'active' : null}}">
                    <a href="{{ route('register.schedules.index') }}" class="nav-link"><i class="fas fa-calendar-check"></i><span>TKB đã đăng kí</span>
                    </a>
                </li>
            @endcan
            @can('view-admin')
                <li class="menu-header">Người dùng</li>
                <li class="nav-item {{ request()->is('admin/users') ? 'active' : null }}">
                    <a href="{{route('users.index')}}" class="nav-link"><i class="far fa-user"></i>
                        <span>Quản lý người dùng</span></a>
                </li>
                <li class="nav-item {{ request()->is('admin/assignment') ? 'active' : null}}"  >
                    <a class="nav-link"  href="{{route('backend.assignment.index')}}"><i class="fas fa-user-tag"></i>
                        <span>Phân công lịch trực</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="{{route('feedback.index')}}"><i class="fas fa-comment-dots"></i>
                        <span>Quản lý phản ánh</span></a>
                </li>
                <li><a class="nav-link" href="credits.html"><i class="fas fa-envelope"></i>
                        <span>Quản lý góp ý</span></a></li>
                <li class="menu-header">Thông kê</li>
                <li class="nav-item {{ request()->is('admin/statistics/number-session') ? 'active' : null}}">
                        <a class="nav-link" href="{{route('backend.statistics.index')}}"><i class="fas fa-chart-bar"></i>
                            <span>Thống kê số tiết TH</span></a>
                </li>
                <li class="nav-item {{ request()->is('admin/statistics/subject-group') ? 'active' : null}}">
                    <a class="nav-link" href="{{route('backend.statistics.statisticsSubjectGroup')}}"><i class="fas fa fa-table"></i>
                        <span>Thống kê số nhóm TH</span></a>
                </li><br>
            @endcan
            @can('view-gv')
                <li class="menu-header">Phản ánh</li>
                <li class="nav-item">
                    <a href="{{route('feedback.create')}}" class="nav-link"><i class="fas fa-comment-dots"></i><span>Phản ánh</span>
                    </a>
                </li>
            @endcan
            {{-- <li><a class="nav-link" href="credits.html"><i class="fas fa-envelope"></i>
                    <span>Góp ý</span></a></li> --}}
        </ul>
    </aside>
</div>
