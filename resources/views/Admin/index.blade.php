<!DOCTYPE html>
<html lang="en">

<head>
    <script type="text/javascript">
        BASE_URL = "<?php echo url(''); ?>";
    </script>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CA-Customer</title>
    
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminTheme/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="{{ asset('AdminTheme/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminTheme/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('AdminTheme/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('AdminTheme/dist/css/adminlte.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('AdminTheme/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('AdminTheme/plugins/summernote/summernote-bs4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('AdminTheme/mainCSS/style.css') }}">
    

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body
    class="hold-transition sidebar-mini layout-fixed control-sidebar-slide-open sidebar-mini ">

    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('Layouts.sidebar')
        @include('Layouts.nav')
        <div id="content-wrapper" class="content-wrapper">
            @yield('content')
        </div>
    </div>


    {{-- SCRIPTS --}}
   
    <!-- jQuery -->
    <script src="{{ asset('AdminTheme/plugins/jquery/jquery.min.js') }}"></script>
   
    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('AdminTheme/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('AdminTheme/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- daterangepicker -->
    <script src="{{ asset('AdminTheme/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('AdminTheme/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('AdminTheme/plugins/chart.js/Chart.min.js') }}"></script>
   <!-- jQuery Knob Chart -->
    <script src="{{ asset('AdminTheme/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="{{ asset('AdminTheme/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('AdminTheme/plugins/summernote/summernote-bs4.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('AdminTheme/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('AdminTheme/dist/js/adminlte.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('AdminTheme/dist/js/demo.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('AdminTheme/dist/js/pages/dashboard.js') }}"></script>
    <script src="{{ asset('AdminTheme/mainjs/MainJSfile.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>

</html>
