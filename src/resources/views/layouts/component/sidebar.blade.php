<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo ">
        <a href="index.html" class="app-brand-link">

            <span class="app-brand-text demo menu-text fw-bold ms-3">MonitoringApp</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item">
            <a href="{{ url('/') }}" class="menu-link">
                <i class="menu-icon icon-base ti tabler-smart-home"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>
        @if (session('user_role') === 'superadmin_app')
            <li class="menu-item {{ request()->is('company*') ? 'active' : '' }}">
                <a href="{{ url('company') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-layout-board"></i>
                    <div data-i18n="Company">Company{{ session('user_role') }}</div>
                </a>
            </li>
        @endif
        @if (session('user_role') === 'superadmin_company')
            <li class="menu-item {{ request()->is('branch*') ? 'active' : '' }}">
                <a href="{{ url('branch') }}" class="menu-link">
                    <i class="menu-icon icon-base ti tabler-layout-board"></i>
                    <div data-i18n="Branch">Branch</div>
                </a>
            </li>
        @endif
        <!-- Dashboards -->
        @if (session('user_role') === 'superadmin_branch')
            @php
                $masterDataActive = request()->is('management-users*', 'schedule-shift*', 'schedule-list*', 'tasks*' , 'master-patroli*');
                $masterReportActive = request()->is('report-absensi*' , 'report-patroli*');

            @endphp
            <li class="menu-item {{ $masterDataActive ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-users"></i>
                    <div data-i18n="Master Data ">Master Data</div>
                    {{-- <div class="badge text-bg-danger rounded-pill ms-auto">5</div> --}}
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('management-users*') ? 'active' : '' }}">
                        <a href="{{ url('management-users') }}" class="menu-link">
                            <div data-i18n="User TAD">User TAD</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('schedule-shift*') ? 'active' : '' }}">
                        <a href="{{ url('schedule-shift') }}" class="menu-link">
                            <div data-i18n="Shift">Shift</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('schedule-list*') ? 'active' : '' }}">
                        <a href="{{ url('schedule-list') }}" class="menu-link">
                            <div data-i18n="Schedule">Schedule</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('tasks*') ? 'active' : '' }}">
                        <a href="{{ url('tasks') }}" class="menu-link">
                            <div data-i18n="Tasks">Tasks</div>
                        </a>
                    </li>
                     <li class="menu-item {{ request()->is('master-patroli*') ? 'active' : '' }}">
                        <a href="{{ url('master-patroli') }}" class="menu-link">
                            <div data-i18n="Master Patroli">Master Patroli</div>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="menu-item {{ $masterReportActive ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon icon-base ti tabler-layout-navbar"></i>
                    <div data-i18n="Report ">Report</div>
                    {{-- <div class="badge text-bg-danger rounded-pill ms-auto">5</div> --}}
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('report-absensi*') ? 'active' : '' }}">
                        <a href="{{ url('report-absensi') }}" class="menu-link">
                            <div data-i18n="Report Absensi">Report Absensi</div>
                        </a>
                    </li>


                </ul>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('report-patroli*') ? 'active' : '' }}">
                        <a href="{{ url('report-patroli') }}" class="menu-link">
                            <div data-i18n="Report Patroli">Report Patroli</div>
                        </a>
                    </li>


                </ul>
            </li>
        @endif

        <!-- Layouts -->





    </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
        <i class="ti tabler-menu icon-base"></i>
        <i class="ti tabler-chevron-right icon-base"></i>
    </a>
</div>
