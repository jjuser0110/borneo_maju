@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{ route('cost.index') }}">{{ __('sidebar.cost') }} /</a> 
        @if (isset($cost)) {{ __('sidebar.edit') }} @else {{ __('sidebar.create') }} @endif
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <h5 class="card-header">{{ __('sidebar.cost_details') }}</h5>
                <div class="card-body">
                    <form class="row g-3" enctype="multipart/form-data" 
                        @if (isset($cost)) method="post" action="{{ route('cost.update', $cost) }}" 
                        @else method="post" action="{{ route('cost.store') }}" @endif 
                        onsubmit="showLoading()">
                        @csrf

                        <div class="col-md-6">
                            <label class="form-label" for="idr_cost_for_transfer">{{ __('sidebar.idr_cost_for_transfer') }}</label>
                            <input type="number"
                                step="1"
                                min="0"
                                class="form-control"
                                placeholder="{{ __('sidebar.idr_cost_for_transfer_placeholder') }}"
                                name="idr_cost_for_transfer"
                                value="{{ $cost->idr_cost_for_transfer ?? '' }}" 
                                required/>
                        </div>

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
