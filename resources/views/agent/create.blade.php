@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('agent.index') }}">{{ __('sidebar.agent') }} /</a> 
         @if (isset($agent)) {{ __('sidebar.edit') }} @else {{ __('sidebar.create') }} @endif
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">{{ __('sidebar.agent_details') }}</h5>
                <div class="card-body">
                    <form class="row g-3" enctype="multipart/form-data" 
                        @if (isset($agent)) method="post" action="{{ route('agent.update', $agent) }}" 
                        @else method="post" action="{{ route('agent.store') }}" @endif
                        onsubmit="showLoading()">
                        @csrf

                        <div class="col-md-6">
                            <label class="form-label" for="agent_name">{{ __('sidebar.agent_name') }}</label>
                            <input type="text"
                                   class="form-control"
                                   placeholder="{{ __('sidebar.agent_name_placeholder') }}"
                                   name="name"
                                   value="{{ $agent->name ?? '' }}" 
                                   required/>
                        </div>

                        @if(Auth::user()->role_id == 3)
                            <div class="col-md-6">
                                <label class="form-label" for="agent_username">{{ __('sidebar.agent_username') }}</label>
                                @if(isset($agent))
                                    <input type="text"
                                           class="form-control"
                                           placeholder="{{ __('sidebar.agent_username_placeholder') }}"
                                           name="username" 
                                           value="{{ $agent->username ?? '' }}"
                                           readonly
                                    />
                                @else
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon11">{{ Auth::user()->username ?? '' }}</span>
                                        <input type="text" class="form-control" name="username" placeholder="{{ __('sidebar.agent_username_placeholder') }}" aria-label="Username" aria-describedby="basic-addon11">
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="col-md-6">
                                <label class="form-label" for="agent_username">{{ __('sidebar.agent_username') }}</label>
                                <input type="text"
                                       class="form-control"
                                       placeholder="{{ __('sidebar.agent_username_placeholder') }}"
                                       name="username" 
                                       value="{{ $agent->username ?? '' }}"
                                       required
                                       @if(isset($agent)) readonly @endif
                                />
                            </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label" for="password">{{ __('sidebar.password') }}</label>
                            <input type="password"
                                   class="form-control"
                                   placeholder="{{ __('sidebar.password_placeholder') }}"
                                   name="password"
                                   @if(!isset($agent)) required @endif />
                        </div>

                        @if(!isset($agent))
                        <div class="col-md-6">
                            <label class="form-label" for="point">{{ __('sidebar.point') }} 
                                <span class="badge bg-success">{{ __('sidebar.balance') }}: {{ Auth::user()->point ?? 'unlimited' }}</span>
                            </label>
                            <input type="number"
                                   min="0"
                                   max="{{ Auth::user()->point ?? '9999999999999' }}"
                                   class="form-control"
                                   name="point"
                                   value="{{ $agent->point ?? '' }}" 
                                   required/>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label" for="idr_rate">{{ __('sidebar.idr_rate') }}
                                <span class="badge bg-success">{{ __('sidebar.rate') }}: {{ Auth::user()->idr_rate ?? 'unlimited' }}</span>
                            </label>
                            <input type="number"
                                   min="1"
                                   max="{{ Auth::user()->idr_rate ?? '9999999999999' }}"
                                   class="form-control"
                                   name="idr_rate"
                                   value="{{ $agent->idr_rate ?? '' }}" 
                                   required/>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="processing_fees">{{ __('sidebar.processing_fees') }}
                                <span class="badge bg-success">{{ __('sidebar.fees') }}: {{ Auth::user()->processing_fees ?? 'unlimited' }}</span>
                            </label>
                            <input type="number"
                                   min="{{ Auth::user()->processing_fees ?? '1' }}"
                                   class="form-control"
                                   name="processing_fees"
                                   value="{{ $agent->processing_fees ?? '' }}" 
                                   required/>
                        </div>

                        @if(isset($agent))
                        <div class="col-md-6">
                            <label class="form-label" for="is_active">{{ __('sidebar.is_active') }}</label>
                            <select name="is_active" class="form-control">
                                <option value="1" <?php echo isset($agent) && $agent->is_active == 1 ? 'selected' : '' ?>>
                                    {{ __('sidebar.active') }}
                                </option>
                                <option value="0" <?php echo isset($agent) && $agent->is_active == 0 ? 'selected' : '' ?>>
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
