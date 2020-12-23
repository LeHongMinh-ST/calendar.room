<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.header')
    @yield('css')
    <title>@yield('title')</title>

</head>

<body>
<div id="app">
    <div class="main-wrapper">

        <!-- navbar -->
        @include('includes.navbar')

        <!-- slidebar -->
        @include('includes.sidebar')

        <!-- Main Content -->
        @yield('content')

        @include('includes.main_footer')
    </div>
</div>

@yield('loading')

<!-- General JS Scripts -->
@include('includes.footer')
@yield('script')
@yield('modals')
<!-- Page Specific JS File -->
</body>
</html>
