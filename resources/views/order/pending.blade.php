@extends('layouts.app')
@section('content')
<style>
    
<style>
.bank-summary-section {
    padding: 24px;
    border-top: 1px solid #e7e7e8;
    background: #fff;
}

.bank-summary-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.bank-summary-header h5 {
    font-size: 16px;
    font-weight: 600;
    color: #566a7f;
}

.bank-summary-card {
    background: #f8f9fc;
    border: 1px solid #e7eaf0;
    border-radius: 10px;
    padding: 16px;
    height: 100%;
    transition: all 0.2s ease;
}

.bank-summary-card:hover {
    border-color: #696cff;
    box-shadow: 0 4px 12px rgba(105,108,255,0.10);
    transform: translateY(-2px);
}

.bank-name {
    font-size: 12px;
    color: #8592a3;
    margin-bottom: 8px;
    white-space: normal;
}

.bank-amount {
    font-size: 19px;
    font-weight: 700;
    color: #566a7f;
    overflow-wrap: anywhere;
}

.bank-currency {
    font-size: 11px;
    color: #a1acb8;
    margin-top: 4px;
}

.bank-grand-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    background: #f0f2ff;
    border: 1px solid #dfe2ff;
    border-radius: 10px;
    padding: 20px 24px;
    margin-top: 24px;
}

.total-label {
    font-size: 15px;
    font-weight: 600;
    color: #566a7f;
}

.total-description {
    font-size: 12px;
    color: #8592a3;
    margin-top: 4px;
}

.total-amount {
    font-size: 24px;
    font-weight: 700;
    color: #696cff;
    text-align: right;
    overflow-wrap: anywhere;
}

@media (max-width: 767px) {
    .bank-summary-section {
        padding: 16px;
    }

    .bank-grand-total {
        flex-direction: column;
        align-items: flex-start;
    }

    .total-amount {
        font-size: 20px;
        text-align: left;
    }
}
</style>

    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4">
            <span class="text-muted fw-light">{{ __('sidebar.pending_order') }}</span>
        </h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label">
                    <h5 class="card-title mb-0">{{ __('sidebar.pending_order_listing') }}</h5>
                </div>
                <div class="dt-action-buttons text-end pt-3 pt-md-0">
                </div>
            </div>

            <div class="card-datatable text-nowrap">
                <table class="dt-column-search table table-bordered" id="mytable">
                    <thead>
                        <tr>
                            <th>{{ __('sidebar.no') }}</th>
                            <th>{{ __('sidebar.order_no') }}</th>
                            <th>{{ __('sidebar.name') }}</th>
                            <th>{{ __('sidebar.order_date') }}</th>
                            <th>{{ __('sidebar.bank') }}</th>
                            <th>{{ __('sidebar.idr_amount') }}</th>
                            <th>{{ __('sidebar.status') }}</th>
                            <th>{{ __('sidebar.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order as $index=>$row)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$row->order_no??""}}</td>
                            <td>{{$row->user->name??""}}</td>
                            <td>{{$row->order_datetime??""}}</td>
                            <td>{{$row->bank->bank_name??""}}</td>
                            <td>{{ number_format($row->idr_amount) }}</td>
                            <td>
                                @if ($row->status == 'completed')
                                    <span class="badge bg-success">{{ $row->status }}</span>
                                @elseif ($row->status == 'cancelled')
                                    <span class="badge bg-danger">{{ $row->status }}</span>
                                @else
                                    <span class="badge bg-warning">{{ $row->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('order.view',$row) }}">{{ __('sidebar.handle') }}</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            
            <div class="card-header flex-column flex-md-row">
                <div class="bank-summary-section">
                    <div class="bank-summary-header">
                        <h5 class="mb-0">
                            <i class="bx bx-buildings me-2"></i>
                            {{ __('sidebar.order_by_bank') }}
                        </h5>
                        <span class="badge bg-label-primary">
                            {{ $orderByBank->count() }} Banks
                        </span>
                    </div>

                    <div class="row g-3">
                        @foreach($orderByBank as $bank)
                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                                <div class="bank-summary-card">
                                    <div class="bank-name">
                                        {{ $bank['bank_name'] }}
                                    </div>
                                    <div class="bank-amount">
                                        {{ number_format($bank['total_idr_amount']) }}
                                    </div>
                                    <div class="bank-currency">IDR</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bank-grand-total">
                        <div>
                            <div class="total-label">
                                {{ __('sidebar.total') }}
                            </div>
                            <div class="total-description">
                                Total amount across all banks
                            </div>
                        </div>
                        <div class="total-amount">
                            IDR {{ number_format($orderByBank->sum('total_idr_amount')) }}
                        </div>
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
