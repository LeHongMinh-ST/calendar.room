<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.header')
    @yield('css')
    <title>@yield('title')</title>
</head>

<body>
<div id="app">
    @yield('content')
</div>

<!-- General JS Scripts -->
@include('includes.footer')
@yield('script')

<!-- Page Specific JS File -->
</body>
</html>
