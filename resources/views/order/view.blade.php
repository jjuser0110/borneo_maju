@extends('layouts.app')
@section('content')
<style>
@media print {
    body * {
        visibility: hidden;
    }

    #printableArea, #printableArea * {
        visibility: visible;
    }

    #printableArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 5px 5px 20px 5px; /* extra bottom padding */
        font-size: 18pt;           /* bigger text */
        line-height: 1;
        font-weight: bold;
        color: #000;
        font-family: monospace;    /* aligns numbers nicely */
        word-wrap: break-word;
    }

    #printableArea .company-name {
        text-align: center;
        font-size: 20pt;           /* slightly bigger for company */
        margin-bottom: 10px;
        font-weight: bold;
    }

    /* Lists and table formatting */
    #printableArea ul {
        padding-left: 0;
        list-style: none;
    }

    #printableArea li {
        margin-bottom: 5px;
    }

    .no-print {
        display: none !important;
    }

    @page {
        size: auto;
        margin: 5mm;
    }
}
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="">{{ $order->order_no ?? '' }}</span>
    </h4>

    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6">
            <!-- About User -->
            <div class="card mb-4" id="printableArea">
                <div class="card-body">
                    <div class="company-name">BORNEO MAJU</div>
                    <hr>
                    <div class="dt-buttons">
                        <a class="dt-button create-new btn btn-primary no-print"
                        type="button"
                        style="float:right;color:white"
                        onclick="printDiv()">
                            <span>
                                <i class="bx bx-printer me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">{{ __('sidebar.print') }}</span>
                            </span>
                        </a>
                    </div>
                    <p class="card-text text-uppercase">{{ __('sidebar.details') }}</p>
                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.order_no') }}:</span>
                            <span>{{ $order->order_no ?? '' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.user') }}:</span>
                            <span>{{ $order->user->username ?? '' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-star bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.order_date') }}:</span>
                            <span>{{ $order->order_datetime ?? '' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-star bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.order_full_date') }}:</span>
                            <span>{{ $order->created_at ?? '' }}</span>
                        </li>
                    </ul>

                    <p class="card-text text-uppercase">{{ __('sidebar.bank_info') }}</p>
                    <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-flag bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.bank') }}:</span>
                            <span>{{ $order->bank->bank_name ?? '' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-detail bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.account_no') }}:</span>
                            <span>{{ $order->account_no ?? '' }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-detail bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.fullname') }}:</span>
                            <span>{{ $order->fullname ?? '' }}</span>
                        </li>
                    </ul>

                    <small class="text-muted text-uppercase">{{ __('sidebar.amount') }}</small>
                    <ul class="list-unstyled mt-3 mb-0">
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bxl-github text-primary me-2"></i>
                            <div class="d-flex flex-wrap">
                                <span class="fw-medium me-2">{{ __('sidebar.processing_fees') }}</span>
                                <span>{{ number_format($order->processing_fees ?? 0, 2) }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bxl-github text-primary me-2"></i>
                            <div class="d-flex flex-wrap">
                                <span class="fw-medium me-2">{{ __('sidebar.total_myr') }}</span>
                                <span>{{ number_format($order->total_amount ?? 0, 2) }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bxl-github text-primary me-2"></i>
                            <div class="d-flex flex-wrap">
                                <span class="fw-medium me-2">MYR</span>
                                <span>{{ number_format($order->myr_amount ?? 0, 2) }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bxl-react text-info me-2"></i>
                            <div class="d-flex flex-wrap">
                                <span class="fw-medium me-2">{{ __('sidebar.idr_rate') }}</span>
                                <span>{{ $order->idr_rate ?? 0 }}</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bxl-react text-info me-2"></i>
                            <div class="d-flex flex-wrap">
                                <span class="fw-medium me-2">{{ __('sidebar.idr_amount') }}</span>
                                <span>{{ number_format($order->idr_amount ?? 0) }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 col-md-6">
            <!-- Profile Overview -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('order.pending_update', $order->id) }}" enctype="multipart/form-data">
                        @csrf
                        <p class="card-text text-uppercase">{{ __('sidebar.work') }}</p>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-center mb-3">
                                <i class="bx bx-check bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.order_remarks') }}:</span>
                                <span>{{ $order->order_remarks ?? '' }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bx bx-check bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.status') }}:</span>
                                <span style="width:100%">
                                    @if((isset($view) && $view) || in_array($order->status, ['completed', 'cancelled']))
                                        @if ($order->status == 'completed')
                                            <span class="badge bg-success">{{ $order->status }}</span>
                                        @elseif ($order->status == 'cancelled')
                                            <span class="badge bg-danger">{{ $order->status }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ $order->status }}</span>
                                        @endif
                                    @else
                                        <select class="form-control" name="status">
                                            <option value="pending" {{ $order->status=='pending'?'selected':'' }}>{{ __('sidebar.pending') }}</option>
                                            <option value="processing" {{ $order->status=='processing'?'selected':'' }}>{{ __('sidebar.processing') }}</option>
                                            <option value="completed" {{ $order->status=='completed'?'selected':'' }}>{{ __('sidebar.completed') }}</option>
                                            <option value="cancelled" {{ $order->status=='cancelled'?'selected':'' }}>{{ __('sidebar.cancelled') }}</option>
                                        </select>
                                    @endif
                                </span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bx bx-check bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.status_at') }}:</span>
                                <span>{{ $order->status_at ?? '' }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bx bx-star bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.status_by') }}:</span>
                                <span>{{ $order->status_by->username ?? '' }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.bank') }}:</span>
                                @if((isset($view) && $view) || in_array($order->status, ['completed', 'cancelled']))
                                    <span>{{ $order->bankSetting->owner_name ?? '' }}</span>
                                @else
                                    <select class="form-control" name="bank_setting_id">
                                        <option value="">-- Select Bank --</option>
                                        @foreach ($bankSettings as $bankSetting)
                                            <option value="{{ $bankSetting->id }}" {{ $order->bank_setting_id == $bankSetting->id ? 'selected' : '' }}>{{ $bankSetting->owner_name }} (IDR {{ number_format($bankSetting->amount) }})</option>
                                        @endforeach
                                    </select>
                                @endif
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.idr_cost_for_transfer') }}:</span>
                                @if((isset($view) && $view) || in_array($order->status, ['completed', 'cancelled']))
                                    <span>{{ number_format($order->idr_cost_for_transfer ?? 0) }}</span>
                                @else
                                    <select class="form-control" name="idr_cost_for_transfer">
                                        <option value="">-- Select Cost --</option>
                                        @foreach ($cost as $cost)
                                            <option value="{{ $cost->idr_cost_for_transfer }}" {{ $order->idr_cost_for_transfer == $cost->idr_cost_for_transfer ? 'selected' : '' }}>(IDR {{ number_format($cost->idr_cost_for_transfer) }})</option>
                                        @endforeach
                                    </select>
                                @endif
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.remarks') }}:</span>
                                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                    <span style="width:100%"><textarea class="form-control" name="remarks">{{ $order->remarks ?? '' }}</textarea></span>
                                @else
                                    <span style="width:100%">{{ $order->remarks ?? '' }}</span>
                                @endif
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">{{ __('sidebar.receipt') }}:</span>
                                @if(isset($order) && isset($order->last_image))
                                    <a href="{{ asset('storage/'.$order->last_image->file_path) }}" target="_blank">{{ $order->last_image->file_name ?? '' }}</a>
                                    @if(!isset($view) || !$view)
                                        <a style="color:red" onclick="if(confirm('{{ __('sidebar.confirm_delete') }}')){window.location.href='{{ route('removeimage',$order->last_image->id) }}'}"><i class="bx bx-trash"></i></a>
                                    @endif
                                @else
                                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                                        <span style="width:100%"><input type="file" class="form-control" name="receipt" accept="image/*,application/pdf"></span>
                                    @else
                                        -
                                    @endif
                                @endif
                            </li>
                        </ul>

                        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                            <button type="submit" class="btn btn-primary mt-3" style="float:right">{{ __('sidebar.update_order') }}</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        @if (isset($order))
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <p class="card-text text-uppercase">{{ __('sidebar.details') }}</p>
                    <div class="card-datatable text-nowrap">
                        <table class="dt-column-search table table-bordered" id="mytable">
                            <thead>
                                <tr>
                                    <th>{{ __('sidebar.user') }}</th>
                                    <th>{{ __('sidebar.idr_amount') }}</th>
                                    <th>{{ __('sidebar.idr_rate') }}</th>
                                    <th>{{ __('sidebar.myr_amount') }}</th>
                                    <th>{{ __('sidebar.processing_fees') }}</th>
                                    <th>{{ __('sidebar.do_up') }}</th>
                                    <th>{{ __('sidebar.profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $details = Auth::user()->role_id == 3
                                        ? $order->details->where('user_id', Auth::id())
                                        : $order->details;
                                @endphp

                                @foreach($details as $row)
                                    <tr>
                                        <td>{{ $row->user->username ?? '' }}</td>
                                        <td>{{ number_format($row->idr_amount) }}</td>
                                        <td>{{ $row->idr_rate ?? '' }}</td>
                                        <td>{{ number_format($row->myr_amount, 2) }}</td>
                                        <td>{{ number_format($row->processing_fees, 2) }}</td>
                                        <td>{{ number_format($row->do_up, 2) }}</td>
                                        <td>{{ number_format($row->profit, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@section('page-js')
@endsection
@section('scripts')
<script>
$(function(){
  var table = $('#mytable').DataTable({
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    paging: false,      // disable pagination
    searching: false,   // disable search/filter
    ordering: false,    // disable column sorting
    info: false,        // hide "Showing X of Y entries"
    lengthChange: false
  });
});

function printDiv() {
    window.print();
}
</script>
@endsection
