@php
$currentRoute = request()->route()->getName();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('smalllogo.png') }}" alt="Logo" style="width:100%;" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2" style="color:#b8b800;font-size:20px">
                Borneo Maju
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
            <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>
    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        {{-- Dashboard --}}
        @if(Auth::user()->role_id == 1)
        <li class="menu-item {{ Str::contains($currentRoute, 'home') ? 'active' : ''}}">
            <a href="{{ route('home') }}" class="menu-link" onclick="showLoading()">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>{{ __('sidebar.dashboard') }}</div>
            </a>
        </li>
        @endif

        {{-- Pendings --}}
        @if(Auth::user()->role_id != 3)
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('sidebar.pendings') }}</span>
        </li>

        <li class="menu-item {{ Str::contains($currentRoute, 'order.pending') ? 'active' : ''}}">
            <a href="{{ route('order.pending') }}" class="menu-link" onclick="showLoading()">
                <i class="menu-icon tf-icons bx bx-time-five"></i>
                <div>{{ __('sidebar.pending_orders') }}</div>
            </a>
        </li>
        @endif

        {{-- Orders --}}
        @if(Auth::user()->role_id != 2)
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('sidebar.orders_section') }}</span>
        </li>

        <li class="menu-item {{ Str::contains($currentRoute, 'report.sales_report') ? 'active' : ''}}">
            <a href="{{ route('report.sales_report') }}" class="menu-link" onclick="showLoading()">
                <i class="menu-icon tf-icons bx bx-line-chart"></i>
                <div>{{ __('sidebar.sales_report') }}</div>
            </a>
        </li>

        <li class="menu-item {{ Str::contains($currentRoute, 'order.index') ? 'active' : ''}}">
            <a href="{{ route('order.index') }}" class="menu-link" onclick="showLoading()">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div>{{ __('sidebar.orders') }}</div>
            </a>
        </li>

        {{-- Settings --}}
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">{{ __('sidebar.settings') }}</span>
        </li>

        <li class="menu-item {{ Str::contains($currentRoute, 'agent.index') ? 'active' : ''}}">
            <a href="{{ route('agent.index') }}" class="menu-link" onclick="showLoading()">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div>{{ __('sidebar.agents') }}</div>
            </a>
        </li>
        @endif

        {{-- Admin --}}
        @if(Auth::user()->role_id == 1)
        @php
            $userRoutes = ['admin', 'staff'];
            $isUserActive = collect($userRoutes)->contains(fn($r) => Str::contains($currentRoute, $r));
        @endphp

        <li class="menu-item {{ $isUserActive ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>{{ __('sidebar.users') }}</div>
            </a>

            <ul class="menu-sub">
                @foreach ($userRoutes as $role)
                <li class="menu-item {{ Str::contains($currentRoute, $role) ? 'active' : '' }}">
                    <a href="{{ route($role . '.index') }}" class="menu-link" onclick="showLoading()">
                        <div>
                            <i class="bx {{ $role === 'admin' ? 'bx-shield' : 'bx-id-card' }} me-2"></i>
                            {{ __('sidebar.' . $role) }}
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </li>

        <li class="menu-item {{ Str::contains($currentRoute, 'bank.index') ? 'active' : ''}}">
            <a href="{{ route('bank.index') }}" class="menu-link" onclick="showLoading()">
                <i class="menu-icon tf-icons bx bx-building-house"></i>
                <div>{{ __('sidebar.banks') }}</div>
            </a>
        </li>
        @endif
    </ul>
</aside>
