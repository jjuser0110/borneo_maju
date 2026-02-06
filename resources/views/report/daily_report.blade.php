@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4">
            <span class="text-muted fw-light">{{ __('sidebar.daily_report') }}</span>
        </h4>

        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="col-md-6 col-12 mb-4">
                    <form method="GET">
                        <div class="input-group input-daterange">
                            <input type="date" class="form-control" name="date" value="{{ $date ?? date('Y-m-d') }}"/>
                            <button class="btn btn-primary" type="submit">{{ __('sidebar.filter') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info"><i class="fa-solid fa-money-bill"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">IDR {{ number_format($stock_in,2) }}</h4>
                        </div>
                        <p class="mb-1">{{ __('sidebar.stock_in') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-danger h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger"><i class="fa-solid fa-money-bill"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">IDR {{ number_format($stock_out,2) }}</h4>
                        </div>
                        <p class="mb-1">{{ __('sidebar.stock_out') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger"><i class="fa-solid fa-money-bill"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">MYR {{ number_format($capital_used, 2) }}</h4>
                        </div>
                        <p class="mb-1">{{ __('sidebar.capital_used') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-info"><i class="fa-solid fa-money-bill"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">MYR {{ number_format($amount_received, 2) }}</h4>
                        </div>
                        <p class="mb-1">{{ __('sidebar.amount_received') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-success"><i class="fa-solid fa-money-bill"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">MYR {{ number_format($profit, 2) }}</h4>
                        </div>
                        <p class="mb-1">{{ __('sidebar.total_profit') }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- / Content -->
@endsection

@section('page-js')
@endsection

@section('scripts')
@endsection
