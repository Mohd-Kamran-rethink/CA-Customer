<!DOCTYPE html>
<html lang="en">

<head>
    <script type="text/javascript">
        BASE_URL = "<?php echo url(''); ?>";
    </script>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CA Customer Care</title>

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
    <link rel="stylesheet"
        href="{{ asset('AdminTheme/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
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

<body class="hold-transition sidebar-mini layout-fixed control-sidebar-slide-open sidebar-mini ">

    <!-- Page Wrapper -->
    <div id="wrapper">
        @include('Layouts.sidebar')
        @include('Layouts.nav')
        <div id="content-wrapper" class="content-wrapper">
            @yield('content')
        </div>
    </div>

    {{-- client add pop up --}}
    <div class="modal fade show" id="client-modal" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add Client</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="m-2" action="{{ url('/clients/add') }}" method="POST"id="client-add-form-popup">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="userId" value="{{ isset($client) ? $client->id : '' }}">
                        <h5 style="color:red;display: none" class="px-2" id="client-error-note">Please fill all input
                        </h5>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Client Name <span style="color:red">*</span></label>
                                <input type="text" id="client_name" name="name" placeholder="John"
                                    class="form-control" data-validation="required"
                                    value="{{ isset($client) ? $client->name : old('name') }}">
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Phone <span style="color:red">*</span></label>
                                <input {{ isset($client) ? 'readonly' : '' }} type="number" id="cliet_number"
                                    name="number" value="{{ isset($client) ? $client->number : old('number') }}"
                                    id="number" placeholder="972873818" class="form-control"
                                    data-validation="required">
                                <span id="client_name_error" class="text-danger">
                                        
                                    </span>
                                

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>ID Name <span style="color:red">*</span></label>
                                <input type="text" id="client_ca_id" name="ca_id"
                                    {{ isset($client) ? 'readonly' : '' }}
                                    value="{{ isset($client) ? $client->ca_id : old('ca_id') }}" id="ca_id"
                                    placeholder="ID Name" class="form-control">
                                @error('ca_id')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button onclick="submitClientAdd()" type="submit" id="submit-button"
                                class="btn btn-info">Save</button>
                            <button type="button" class="btn btn-primary " data-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- SCRIPTS --}}
    <script>
        function submitClientAdd() {
            event.preventDefault();
            let submitButton = $('#submit-button')
            let client_name = $('#client_name').val();
            let client_number = $('#cliet_number').val();
            let client_ca_id = $('#client_ca_id').val();

                $.ajax({
                    url: BASE_URL + "/clients/add?name=" + client_name + "&number=" + client_number + "&ca_id=" +
                        client_ca_id,
                    success: function(data) {
                        if (data) {
                            $('#client-modal').modal('hide');
                            $("#client-ajax-dropdown").html(data);
                        } 
                       },
                       error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.errors) {
                       
                        // Loop through each error and display it on the respective input field
                        $.each(response.errors, function(key, value) {
                            console.log(key)
                            var inputElement = $('#' + key);
                            inputElement.addClass('is-invalid');
                            inputElement.next('.invalid-feedback').html(value[0]);
                        });
                    } else {
                        // Handle other error cases
                    }
                }
                });
            


        }
    </script>
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
