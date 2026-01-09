@php
    $user_id = Auth::user()->id;
    $data1 = DB::table('role_menu as rm')
        ->selectRaw(
            'DISTINCT ON (m.id)
                            u.name as name_user,
                            rm.id as role_menu_id,
                            r.title as role,
                            m.id as menu_id,
                            m.name as menu_name,
                            m.url
                        ',
        )
        ->leftJoin('role as r', 'r.id', '=', 'rm.role_id')
        ->leftJoin('menu as m', 'm.id', '=', 'rm.menu_id')
        ->leftJoin('role_user as ru', 'ru.role_id', '=', 'r.id')
        ->leftJoin('users as u', 'u.id', '=', 'ru.user_id')
        ->where('u.id', $user_id)
        ->where('m.parent_id', 1)
        ->orderBy('m.id')
        ->orderBy('rm.id')
        ->get();
    $user_id = Auth::user()->id;
    $data2 = DB::table('role_menu as rm')
        ->selectRaw(
            'DISTINCT ON (m.id)
                            u.name as name_user,
                            rm.id as role_menu_id,
                            r.title as role,
                            m.id as menu_id,
                            m.name as menu_name,
                            m.url
                        ',
        )
        ->leftJoin('role as r', 'r.id', '=', 'rm.role_id')
        ->leftJoin('menu as m', 'm.id', '=', 'rm.menu_id')
        ->leftJoin('role_user as ru', 'ru.role_id', '=', 'r.id')
        ->leftJoin('users as u', 'u.id', '=', 'ru.user_id')
        ->where('u.id', $user_id)
        ->where('m.parent_id', 2)
        ->orderBy('m.id')
        ->orderBy('rm.id')
        ->get();

@endphp

@if ($data1)
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-users"></i>
            <div data-i18n="Master Data ">Master Data</div>
            {{-- <div class="badge text-bg-danger rounded-pill ms-auto">5</div> --}}
        </a>
        <ul class="menu-sub">
            @foreach ($data1 as $item)
                <li class="menu-item ">
                    <a href="{{ url($item->url) }}" class="menu-link">
                        <div data-i18n="{{ $item->menu_name }}">{{ $item->menu_name }}</div>
                    </a>
                </li>
            @endforeach



        </ul>
    </li>
@endif

@if ($data2)
    <li class="menu-item ">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon icon-base ti tabler-layout-navbar"></i>
            <div data-i18n="Report ">Report</div>
            {{-- <div class="badge text-bg-danger rounded-pill ms-auto">5</div> --}}
        </a>
        <ul class="menu-sub">
            @foreach ($data2 as $item)
                <li class="menu-item ">
                    <a href="{{ url($item->url) }}" class="menu-link">
                        <div data-i18n="{{ $item->menu_name }}">{{ $item->menu_name }}</div>
                    </a>
                </li>
            @endforeach



        </ul>
    </li>
@endif
