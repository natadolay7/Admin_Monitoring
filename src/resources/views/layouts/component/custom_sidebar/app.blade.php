<li class="menu-item {{ request()->is('v1/company*') ? 'active' : '' }}">
    <a href="{{ url('v1/company') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-building"></i>
        <div data-i18n="Company">Company</div>
    </a>
</li>
<li class="menu-item {{ request()->is('v1/branch*') ? 'active' : '' }}">
    <a href="{{ url('v1/branch') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-layout-board"></i>
        <div data-i18n="Branch">Branch</div>
    </a>
</li>
@php
    $masterDataActive = request()->is(
        'v1/management-users*',
        // 'schedule-shift*',
        // 'schedule-list*',
        // 'tasks*',
        // 'master-patroli*',
        // 'master-pengumuman*',
    );
    $masterReportActive = request()->is('v1/report-absensi*', 'report-patroli*');
    $coreActive = request()->is('core/role*', 'core/menu-has-role*', 'core/users*');

@endphp
<li class="menu-item {{ $masterDataActive ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-users"></i>
        <div data-i18n="Master Data ">Master Data</div>
        {{-- <div class="badge text-bg-danger rounded-pill ms-auto">5</div> --}}
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ request()->is('v1/management-users*') ? 'active' : '' }}">
            <a href="{{ url('v1/management-users') }}" class="menu-link">
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
        <li class="menu-item {{ request()->is('master-pengumuman*') ? 'active' : '' }}">
            <a href="{{ url('master-pengumuman') }}" class="menu-link">
                <div data-i18n="Master Pengumuman">Master Pengumuman</div>
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
        <li class="menu-item {{ request()->is('v1/report-absensi*') ? 'active' : '' }}">
            <a href="{{ url('v1/report-absensi') }}" class="menu-link">
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
<li class="menu-item {{ $coreActive ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon icon-base ti tabler-settings"></i>
        <div data-i18n="Core ">Core</div>
        {{-- <div class="badge text-bg-danger rounded-pill ms-auto">5</div> --}}
    </a>
    <ul class="menu-sub">
        <li class="menu-item {{ request()->is('core/role*') ? 'active' : '' }}">
            <a href="{{ url('core/role') }}" class="menu-link">
                <div data-i18n="Role">Role</div>
            </a>
        </li>


    </ul>
    <ul class="menu-sub">
        <li class="menu-item {{ request()->is('core/menu-has-role*') ? 'active' : '' }}">
            <a href="{{ url('core/menu-has-role') }}" class="menu-link">
                <div data-i18n="Menu Has Role">Menu Has Role</div>
            </a>
        </li>
    </ul>
    <ul class="menu-sub">
        <li class="menu-item {{ request()->is('core/users*') ? 'active' : '' }}">
            <a href="{{ url('core/users') }}" class="menu-link">
                <div data-i18n="Users">Users</div>
            </a>
        </li>
    </ul>
</li>
<li class="menu-item {{ request()->is('leave*') ? 'active' : '' }}">
    <a href="{{ url('/leave') }}" class="menu-link">
        <i class="menu-icon icon-base ti tabler-calendar-time"></i>
        <div data-i18n="Leave">Leave</div>
    </a>
</li>
