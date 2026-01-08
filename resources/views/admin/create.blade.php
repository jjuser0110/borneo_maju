@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('admin.index') }}">{{ __('sidebar.admin') }} /</a> 
        @if (isset($admin)) {{ __('sidebar.edit') }} @else {{ __('sidebar.create') }} @endif
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">{{ __('sidebar.admin_details') }}</h5>
                <div class="card-body">
                    <form class="row g-3" enctype="multipart/form-data" 
                        @if (isset($admin)) method="post" action="{{ route('admin.update', $admin) }}" 
                        @else method="post" action="{{ route('admin.store') }}" @endif
                        onsubmit="showLoading()">
                        @csrf

                        <div class="col-md-7">
                            <label class="form-label" for="admin_name">{{ __('sidebar.admin_name') }}</label>
                            <input type="text"
                                   class="form-control"
                                   placeholder="{{ __('sidebar.admin_name_placeholder') }}"
                                   name="name"
                                   value="{{ $admin->name ?? '' }}" 
                                   required/>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label" for="admin_username">{{ __('sidebar.admin_username') }}</label>
                            <input type="text"
                                   class="form-control"
                                   placeholder="{{ __('sidebar.admin_username_placeholder') }}"
                                   name="username" 
                                   value="{{ $admin->username ?? '' }}"
                                   required
                                   @if(isset($admin)) readonly @endif
                            />
                        </div>

                        <div class="col-md-7">
                            <label class="form-label" for="password">{{ __('sidebar.password') }}</label>
                            <input type="password"
                                   class="form-control"
                                   placeholder="{{ __('sidebar.password_placeholder') }}"
                                   name="password"
                                   @if(!isset($admin)) required @endif />
                        </div>

                        @if(isset($admin))
                        <div class="col-md-7">
                            <label class="form-label" for="is_active">{{ __('sidebar.is_active') }}</label>
                            <select name="is_active" class="form-control">
                                <option value="1" <?php echo isset($admin)&&$admin->is_active == 1?'selected':'' ?>>
                                    {{ __('sidebar.active') }}
                                </option>
                                <option value="0" <?php echo isset($admin)&&$admin->is_active == 0?'selected':'' ?>>
                                    {{ __('sidebar.inactive') }}
                                </option>
                            </select>
                        </div>
                        @endif

                        <hr>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('sidebar.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->
@endsection

@section('scripts')
@endsection
