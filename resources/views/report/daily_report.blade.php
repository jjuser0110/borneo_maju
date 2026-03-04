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

        @isset($bankSettings)
            <div class="card mb-3">
                <div class="card-header flex-column flex-md-row">
                    <div class="head-label">
                        <h5 class="card-title mb-0">Bank Settings</h5>
                    </div>
                </div>
                <div class="card-datatable text-nowrap">
                    <table class="dt-column-search table table-bordered" id="mytable">
                        <thead>
                            <tr>
                                <th>{{ __('sidebar.bank_name') }}</th>
                                <th>{{ __('sidebar.account_no') }}</th>
                                <th>{{ __('sidebar.stock_in') }}</th>
                                <th>{{ __('sidebar.stock_out') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bankSettings as $bank)
                                @php
                                    $log = $bankLogs[$bank->id] ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $bank->bank->bank_name ?? "" }}</td>
                                    <td><a href="{{ route('bank_setting.viewlog', $bank->id) }}?date={{ $date ?? date('Y-m-d') }}">{{ $bank->account_no ?? "" }}</a></td>
                                    <td style="color:green; text-align:right;">
                                        {{ number_format($log->total_stock_in ?? 0) }}
                                    </td>

                                    <td style="color:red; text-align:right;">
                                        {{ number_format($log->total_stock_out ?? 0) }}
                                    </td>
                            @endforeach
                        </tbody>
                        @php
                            $grandIn = $bankLogs->sum('total_stock_in');
                            $grandOut = $bankLogs->sum('total_stock_out');
                        @endphp
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end"><strong>Total</strong></td>
                                <td class="text-end text-success"><strong>{{ number_format($grandIn) }}</strong></td>
                                <td class="text-end text-danger"><strong>{{ number_format($grandOut) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endisset

    </div>
    <!-- / Content -->
@endsection

@section('page-js')
@endsection

@section('scripts')
@endsection
