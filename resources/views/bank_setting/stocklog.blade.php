@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">{{ __('sidebar.bank_setting') }} ({{ $stock->bankSetting->owner_name ?? '' }}) /</span>
        Stock
    </h4>

    <!-- DataTable with Buttons -->
    <div class="card mb-3">
        <div class="card-header flex-column flex-md-row">
            <div class="head-label">
                <h5 class="card-title mb-0">
                    {{ __('sidebar.stock_logs') }} <span style="color:green">IDR {{ number_format($stock->idr_balance ?? 0) }}</span>
                </h5>
            </div>
        </div>
        <div class="card-datatable text-nowrap">
            <table class="dt-column-search table table-bordered" id="mytable">
                <thead>
                    <tr>
                        <th>{{ __('sidebar.no') }}</th>
                        <th>{{ __('sidebar.myr_amount') }}</th>
                        <th>{{ __('sidebar.idr_rate') }}</th>
                        <th>{{ __('sidebar.idr_amount') }}</th>
                        <th>{{ __('sidebar.capital_used') }}</th>
                        <th>{{ __('sidebar.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stock->stock_logs as $index => $log)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $log->order->order_no ?? "" }}</td>
                        <td>{{ $log->idr_rate ?? 0 }}</td>
                        <td>{{ number_format($log->idr_amount ?? 0) }}</td>
                        <td>{{ number_format($log->capital_used ?? 0, 2) }}</td>
                        <td>{{ $log->created_at ?? "" }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- / Content -->
@endsection

@section('page-js')
@endsection

@section('scripts')
<script>
$(function(){
  var table = $('#mytable').DataTable({
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    pageLength: 10,
    displayLength: 5,
    ordering:false,
    lengthMenu: [5, 10, 25, 50, 75, 100],
  });
});
</script>
@endsection
