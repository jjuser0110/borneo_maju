@php

$currentRoute = request()->route()->getName();

@endphp
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="{{ asset('smalllogo.png') }}" alt="Logo" style="width:100%;" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2" style="color:#b8b800;font-size:20px">Borneo Maju</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
            <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        @if(Auth::user()->role_id == 1)
        <li class="menu-item {{ Str::contains($currentRoute, 'home') ? 'active' : ''}}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboards</div>
            </a>
        </li>
        @endif
        @if(Auth::user()->role_id != 3)
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" data-i18n="Orders">Pendings</span>
        </li>
        <li class="menu-item {{ Str::contains($currentRoute, 'order.pending') ? 'active' : ''}}">
            <a href="{{ route('order.pending') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Pending Orders</div>
            </a>
        </li>
        @endif
        
        @if(Auth::user()->role_id != 2)
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" data-i18n="Orders">Orders</span>
        </li>
        <li class="menu-item {{ Str::contains($currentRoute, 'order.index') ? 'active' : ''}}">
            <a href="{{ route('order.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Orders</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text" data-i18n="Settings">Settings</span>
        </li>
        <li class="menu-item {{ Str::contains($currentRoute, 'agent.index') ? 'active' : ''}}">
            <a href="{{ route('agent.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Agents</div>
            </a>
        </li>
        @endif
        @if(Auth::user()->role_id == 1)
            @php
                $userRoutes = ['admin', 'staff'];

                $isUserActive = collect($userRoutes)->contains(fn($r) => Str::contains($currentRoute, $r));
            @endphp

            @if(count($userRoutes) > 0)
            <li class="menu-item {{ $isUserActive ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user-circle"></i>
                    <div>Users</div>
                </a>

                <ul class="menu-sub">
                    @foreach ($userRoutes as $role)
                        <li class="menu-item {{ Str::contains($currentRoute, $role) ? 'active' : '' }}">
                            <a href="{{ route($role . '.index') }}" class="menu-link">
                                <div>{{ ucfirst($role) }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            @endif
            <li class="menu-item {{ Str::contains($currentRoute, 'bank.index') ? 'active' : ''}}">
                <a href="{{ route('bank.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user-circle"></i>
                    <div>Banks</div>
                </a>
            </li>
        @endif

    </ul>
</aside>