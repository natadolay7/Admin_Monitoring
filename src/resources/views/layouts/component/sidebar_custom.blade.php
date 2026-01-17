@php
    function isMenuActive($url)
    {
        return request()->is(trim($url, '/') . '*') ? 'active' : '';

    }
    $userId = Auth::id();

    function getMenuByParent($parentId, $userId)
    {
        return DB::table('role_menu as rm')
            ->selectRaw(
                'DISTINCT ON (m.id)
                u.name as name_user,
                rm.id as role_menu_id,
                r.title as role,
                m.id as menu_id,
                m.name as menu_name,
                m.url
            '
            )
            ->leftJoin('role as r', 'r.id', '=', 'rm.role_id')
            ->leftJoin('menu as m', 'm.id', '=', 'rm.menu_id')
            ->leftJoin('role_user as ru', 'ru.role_id', '=', 'r.id')
            ->leftJoin('users as u', 'u.id', '=', 'ru.user_id')
            ->where('u.id', $userId)
            ->where('m.parent_id', $parentId)
            ->orderBy('m.id')
            ->orderBy('rm.id')
            ->get();
    }

    $data1 = getMenuByParent(1, $userId); // Master Data
    $data2 = getMenuByParent(2, $userId); // Report
    $data4 = getMenuByParent(4, $userId); // Report

@endphp

@if ($data1->isNotEmpty())
    @php
        $parentActive = false;
        foreach ($data1 as $item) {
            if (request()->is(trim($item->url, '/') . '*')) {
                $parentActive = true;
                break;
            }
        }
    @endphp

    <li class="menu-item {{ $parentActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-users"></i>
            <div data-i18n="Master Data">Master Data</div>
        </a>

        <ul class="menu-sub">
            @foreach ($data1 as $item)
                <li class="menu-item {{ isMenuActive($item->url) }}">
                    <a href="{{ url($item->url) }}" class="menu-link">
                        <div data-i18n="{{ $item->menu_name }}">
                            {{ $item->menu_name }}
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
@endif

@if ($data2->isNotEmpty())
    @php
        $parentActive = false;
        foreach ($data2 as $item) {
            if (request()->is(trim($item->url, '/') . '*')) {
                $parentActive = true;
                break;
            }
        }
    @endphp

    <li class="menu-item {{ $parentActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-layout-navbar"></i>
            <div data-i18n="Report">Report</div>
        </a>

        <ul class="menu-sub">
            @foreach ($data2 as $item)
                <li class="menu-item {{ isMenuActive($item->url) }}">
                    <a href="{{ url($item->url) }}" class="menu-link">
                        <div data-i18n="{{ $item->menu_name }}">
                            {{ $item->menu_name }}
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
@endif

@if ($data4->isNotEmpty())
    @php
        $parentActive = false;
        foreach ($data4 as $item) {
            if (request()->is(trim($item->url, '/') . '*')) {
                $parentActive = true;
                break;
            }
        }
    @endphp

    <li class="menu-item {{ $parentActive ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-settings"></i>
            <div data-i18n="Core">Core</div>
        </a>

        <ul class="menu-sub">
            @foreach ($data4 as $item)
                <li class="menu-item {{ isMenuActive($item->url) }}">
                    <a href="{{ url($item->url) }}" class="menu-link">
                        <div data-i18n="{{ $item->menu_name }}">
                            {{ $item->menu_name }}
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
@endif
