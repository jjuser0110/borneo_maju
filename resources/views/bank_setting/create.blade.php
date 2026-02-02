@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('bank_setting.index') }}">{{ __('sidebar.bank_setting') }} /</a> 
        @if (isset($bank_setting)) {{ __('sidebar.edit') }} @else {{ __('sidebar.create') }} @endif
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">{{ __('sidebar.bank_setting_details') }}</h5>
                <div class="card-body">
                    <form class="row g-3" enctype="multipart/form-data" 
                        @if (isset($bank_setting)) method="post" action="{{ route('bank_setting.update', $bank_setting) }}" 
                        @else method="post" action="{{ route('bank_setting.store') }}" @endif 
                        onsubmit="showLoading()">
                        @csrf

                        <div class="col-md-6">
                            <label for="bank_id" class="form-label">{{ __('sidebar.bank') }}</label>
                            <select id="bank_id" name="bank_id" class="form-control select2" data-allow-clear="true" required>
                                @foreach($bank as $bank)
                                    <option value="{{ $bank->id }}" @if(isset($bank_setting) && $bank_setting->bank_id == $bank->id) selected @endif>
                                        {{ $bank->bank_name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="account_no">{{ __('sidebar.account_no') }}</label>
                            <input type="text"
                                   class="form-control"
                                   placeholder="{{ __('sidebar.account_no_placeholder') }}"
                                   name="account_no"
                                   value="{{ $bank_setting->account_no ?? '' }}" 
                                   required/>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="owner_name">{{ __('sidebar.owner_name') }}</label>
                            <input type="text"
                                   class="form-control"
                                   placeholder="{{ __('sidebar.owner_name_placeholder') }}"
                                   name="owner_name" 
                                   value="{{ $bank_setting->owner_name ?? '' }}"
                                   required/>
                        </div>

                        @if(isset($bank_setting))
                        <div class="col-md-6">
                            <label class="form-label" for="is_active">{{ __('sidebar.is_active') }}</label>
                            <select name="is_active" class="form-control">
                                <option value="1" <?php echo isset($bank_setting) && $bank_setting->is_active == 1 ? 'selected' : '' ?>>
                                    {{ __('sidebar.active') }}
                                </option>
                                <option value="0" <?php echo isset($bank_setting) && $bank_setting->is_active == 0 ? 'selected' : '' ?>>
                                    {{ __('sidebar.inactive') }}
                                </option>
                            </select>
                        </div>
                        @endif

                        <hr>

                        <div class="col-12">
                            <button type="submit" name="submitButton" class="btn btn-primary">
                                {{ __('sidebar.submit') }}
                            </button>
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
