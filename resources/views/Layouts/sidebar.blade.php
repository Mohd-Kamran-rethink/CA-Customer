<style>
    .os-scrollbar-horizontal {
        display: none
    }
</style>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
        <a href="{{ url('/dashboard') }}" class="brand-link">
            <img src="https://imgs.search.brave.com/jjizMxNTRgX8Jd1PNu7XXsh0-_jVVpSJF-bVeHWJZ_c/rs:fit:860:900:1/g:ce/aHR0cHM6Ly93d3cu/a2luZHBuZy5jb20v/cGljYy9tLzc4LTc4/NjIwN191c2VyLWF2/YXRhci1wbmctdXNl/ci1hdmF0YXItaWNv/bi1wbmctdHJhbnNw/YXJlbnQucG5n"
                alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">CA Customer Care</span>
        </a>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                <li class="nav-item  ">
                    <a href="{{ url('/dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                @if (session('user')->role == 'customer_care_manager')
                    @if (session('user')->is_admin == 'Yes')
                        <li class="nav-item ">
                            <a href="{{ url('/managers') }}"
                                class="nav-link {{ Request::is('managers') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-users"></i>
                                <p>
                                    Managers
                                </p>
                            </a>
                        </li>
                    @endif
                    <li
                        class="nav-item {{ Request::is('deposit-banker') || Request::is('withdrawal-banker') || Request::is('deposit-banker') || Request::is('withdrawrers') ? 'menu-is-opening menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ Request::is('deposit-banker') || Request::is('withdrawal-banker') || Request::is('deposit-banker') || Request::is('withdrawrers') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                Agents
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview"
                            style="display: {{ Request::is('deposit-banker') || Request::is('withdrawal-banker') || Request::is('deposit-banker') || Request::is('withdrawrers') ? 'block' : 'none' }}">


                            <li class="nav-item  ">
                                <a href="{{ url('/deposit-banker') }}"
                                    class="nav-link {{ Request::is('deposit-banker') ? 'active' : '' }}">

                                    <p>
                                        Deposit Banker
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item  ">
                                <a href="{{ url('/withdrawal-banker') }}"
                                    class="nav-link {{ Request::is('withdrawal-banker') ? 'active' : '' }}">

                                    <p>
                                        Withdrawal Banker
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item  ">
                                <a href="{{ url('/depositers') }}"
                                    class="nav-link {{ Request::is('depositers') ? 'active' : '' }}">

                                    <p>
                                        Depositers
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item  ">
                                <a href="{{ url('/withdrawrers') }}"
                                    class="nav-link {{ Request::is('withdrawrers') ? 'active' : '' }}">

                                    <p>
                                        Withdrawrers
                                    </p>
                                </a>
                            </li>

                        </ul>
                    </li>
                    <li class="nav-item ">
                        <a href="{{ url('/franchises') }}"
                            class="nav-link {{ Request::is('franchises') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-building"></i>
                            <p>
                                Franchises
                            </p>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a href="{{ url('/bank-accounts') }}"
                            class="nav-link {{ Request::is('bank-accounts') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-money">&#x1F4B0;
                            </i>
                            <p>
                                Bank Accounts
                            </p>
                        </a>
                    </li>
                @endif()
                @if (session('user')->role == 'deposit_banker')
                    <li class="nav-item  ">
                        <a href="{{ url('/transactions/add') }}"
                            class="nav-link {{ Request::is('transactions/add') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-credit-card"></i>
                            <p>
                                Add Transaction
                            </p>
                        </a>
                    </li>
                @endif
                @if (session('user')->role == 'withdrawrer')
                    <li class="nav-item  ">
                        <a href="{{ url('/transactions/withdraw/add') }}"
                            class="nav-link {{ Request::is('transactions/withdraw/add') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-credit-card"></i>
                            <p>
                                Add Withdraw Request
                            </p>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
