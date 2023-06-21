<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ url('dashboard') }}"
                class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">Dashboard</a>
        </li>
    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        {{-- profile --}}
        <li class="nav-item dropdown ">
            <a class="nav-link d-flex flex-row align-items-center " data-toggle="dropdown" href="#">
                <i class="far fa-user p-2"></i>
                {{session('user')->name }} <span style="text-transform: capitalize" class="px-2"> ( {{ str_replace('_', ' ', session('user')->role)}})</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-divider"></div>
                
                <a href="{{ url('/logout') }}" class="dropdown-item">
                    <i class="fas fa-power-off mr-2"></i>Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->
