<div class="logo-container">
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="container-xxl">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                    <i class="bx bx-menu bx-sm"></i>
                </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                <ul class="navbar-nav flex-row align-items-center ms-auto">

                    <a class="btn btn-primary" style="margin-left:5px"
                       href="{{ route('my_point_logs') }}" onclick="showLoading()">
                        {{ __('sidebar.balance') }} : {{ number_format(Auth::user()->point,2) }}
                    </a>

                    <!-- Language -->
                    <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <i class="bx bx-globe bx-sm"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" onclick="change_language('en')" style="cursor:pointer">
                                    <span class="align-middle">{{ __('sidebar.english') }}</span>
                                </a>
                                <a class="dropdown-item" onclick="change_language('cn')" style="cursor:pointer">
                                    <span class="align-middle">{{ __('sidebar.chinese') }}</span>
                                </a>
                                <a class="dropdown-item" onclick="change_language('bm')" style="cursor:pointer">
                                    <span class="align-middle">{{ __('sidebar.bahasa_melayu') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!--/ Language -->

                    <!-- User -->
                    <li class="nav-item navbar-dropdown dropdown-user dropdown">
                        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <div class="avatar avatar-online">
                                <img src="../../assets/img/avatars/1.png" alt class="rounded-circle" />
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="pages-account-settings-account.html">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar avatar-online">
                                                <img src="../../assets/img/avatars/1.png" alt class="rounded-circle" />
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-medium d-block lh-1">
                                                {{ Auth::user()->username ?? '' }}
                                            </span>
                                            <small>{{ Auth::user()->role->title ?? '' }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>

                            <li><div class="dropdown-divider"></div></li>

                            <li>
                                <a class="dropdown-item" style="cursor:pointer" href="{{ route('my_point_logs') }}">
                                    <i class="bx bx-user me-2"></i>
                                    <span class="align-middle">{{ __('sidebar.my_point_logs') }}</span>
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" style="cursor:pointer" data-bs-toggle="modal"
                                   data-bs-target="#basicModal">
                                    <i class="bx bx-user me-2"></i>
                                    <span class="align-middle">{{ __('sidebar.change_password') }}</span>
                                </a>
                            </li>

                            <li><div class="dropdown-divider"></div></li>

                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('logout') }}"
                                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    <i class="bx bx-power-off me-2"></i>
                                    <span class="align-middle">{{ __('sidebar.logout') }}</span>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!--/ User -->

                </ul>
            </div>

            <!-- Search Small Screens -->
            <div class="navbar-search-wrapper search-input-wrapper container-xxl d-none">
                <input type="text" class="form-control search-input border-0"
                       placeholder="{{ __('sidebar.search') }}" aria-label="Search..." />
                <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
            </div>
        </div>
    </nav>
</div>

<script>
function change_language(x){
    showLoading();
    $.ajax({
        data: {language:x},
        url: "{{ route('change_language') }}",
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(response){
            location.reload();
        }
    });
}
</script>
