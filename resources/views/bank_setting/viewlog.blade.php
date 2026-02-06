@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">{{ __('sidebar.bank_setting') }} ({{ $bank_setting->owner_name ?? '' }})</span>
    </h4>

    <!-- DataTable with Buttons -->
    <div class="card mb-3">
        <div class="card-header flex-column flex-md-row">
            <div class="head-label">
                <h5 class="card-title mb-0">
                    {{ __('sidebar.stocks') }} <span style="color:green">IDR {{ number_format($bank_setting->stock_amount ?? 0) }}</span>
                </h5>
            </div>
            <div class="dt-action-buttons text-end pt-3 pt-md-0">
                <div class="dt-buttons">
                    <button class="dt-button create-new btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addStockModal">
                        <span>
                            {{-- <i class="bx bx-plus me-sm-1"></i> --}}
                            <span class="d-none d-sm-inline-block">
                                {{ __('sidebar.add_stock') }}
                            </span>
                        </span>
                    </button>
                </div>
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
                        <th>{{ __('sidebar.balance') }}</th>
                        <th>{{ __('sidebar.created_at') }}</th>
                        <th>{{ __('sidebar.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bank_setting->stocks as $index => $stock)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: right;">{{ number_format($stock->myr_amount ?? 0, 2) }}</td>
                        <td style="text-align: right;">{{ $stock->idr_rate ?? 0 }}</td>
                        <td style="text-align: right;">{{ number_format($stock->idr_amount ?? 0) }}</td>
                        <td style="text-align: right;">{{ number_format($stock->idr_balance ?? 0) }}</td>
                        <td>{{ $stock->created_at ?? "" }}</td>
                        <td>
                            <a href="{{ route('bank_setting.view_stock_log', $stock) }}" onclick="showLoading()">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header flex-column flex-md-row">
            <div class="head-label">
                <h5 class="card-title mb-0">
                    {{ __('sidebar.bank_logs') }} <span style="color:green">IDR {{ number_format($bank_setting->amount ?? 0) }}</span>
                </h5>
            </div>
            <div class="dt-action-buttons text-end pt-3 pt-md-0">
                <div class="dt-buttons">
                    <button class="dt-button create-new btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#adjustAmountModal">
                        <span>
                            {{-- <i class="bx bx-plus me-sm-1"></i> --}}
                            <span class="d-none d-sm-inline-block">
                                {{ __('sidebar.adjust_amount') }}
                            </span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-datatable text-nowrap">
            <table class="dt-column-search table table-bordered" id="mytable2">
                <thead>
                    <tr>
                        <th>{{ __('sidebar.no') }}</th>
                        <th>{{ __('sidebar.description') }}</th>
                        <th>{{ __('sidebar.remarks') }}</th>
                        <th>{{ __('sidebar.before') }}</th>
                        <th>{{ __('sidebar.amount') }}</th>
                        <th>{{ __('sidebar.after') }}</th>
                        <th>{{ __('sidebar.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bank_setting->bank_logs as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ __('sidebar.' . $row->type ?? "") }}</td>
                        <td>{{ $row->remarks ?? "" }}</td>
                        <td style="text-align: right;">{{ number_format($row->prev_amount ?? 0, 2) }}</td>
                        <td @if($row->after_amount > $row->prev_amount) style="color:green; text-align: right;"
                            @elseif($row->after_amount < $row->prev_amount) style="color:red; text-align: right;"
                            @endif>{{ number_format($row->amount ?? 0) }}</td>
                        <td style="text-align: right;">{{ number_format($row->after_amount ?? 0) }}</td>
                        <td>{{ $row->created_at ?? "" }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- / Content -->

<div class="modal fade" id="addStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_setting.addStock') }}" method="POST" onsubmit="showLoading()">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('sidebar.add_stock') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <input type="hidden" name="bank_setting_id" id="bank_setting_id" value="{{ $bank_setting->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.myr_amount') }}</label>
                        <input type="number" name="myr_amount" id="myr_amount" class="form-control" min="0" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.idr_rate') }}</label>
                        <input type="number" name="idr_rate" id="idr_rate" class="form-control" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.idr_amount') }}</label>
                        <input type="number" name="idr_amount" id="idr_amount" class="form-control" min="0" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('sidebar.close') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('sidebar.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="adjustAmountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_setting.adjust_money') }}" method="POST" onsubmit="showLoading()">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('sidebar.adjust_amount') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <input type="hidden" name="bank_setting_id" id="bank_setting_id" value="{{ $bank_setting->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.amount') }}</label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.remarks') }}</label>
                        <input type="text" name="remarks" class="form-control" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('sidebar.close') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('sidebar.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
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
    pageLength: 10,
    displayLength: 5,
    ordering:false,
    lengthMenu: [5, 10, 25, 50, 75, 100],
  });

  var table = $('#mytable2').DataTable({
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
    pageLength: 10,
    displayLength: 5,
    ordering:false,
    lengthMenu: [5, 10, 25, 50, 75, 100],
  });
});
</script>

<script>
    let isUpdating = false;

    const idrRateInput = document.getElementById('idr_rate');
    const myrInput = document.getElementById('myr_amount');
    const idrInput = document.getElementById('idr_amount');

    function round(value, decimals = 2) {
        return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
    }

    myrInput.addEventListener('input', function () {
        if (isUpdating) return;

        const rate = parseFloat(idrRateInput.value);
        const myr = parseFloat(this.value);

        if (!rate || !myr) return;

        isUpdating = true;
        idrInput.value = Math.round(myr * rate);
        isUpdating = false;
    });

    idrInput.addEventListener('input', function () {
        if (isUpdating) return;

        const rate = parseFloat(idrRateInput.value);
        const idr = parseFloat(this.value);

        if (!rate || !idr) return;

        isUpdating = true;
        myrInput.value = round(idr / rate, 2);
        isUpdating = false;
    });

    // Optional: if IDR rate changes, recalc based on MYR
    idrRateInput.addEventListener('input', function () {
        const rate = parseFloat(this.value);
        const myr = parseFloat(myrInput.value);

        if (!rate || !myr) return;

        isUpdating = true;
        idrInput.value = Math.round(myr * rate);
        console.log(Math.round(myr * rate));
        isUpdating = false;
    });
</script>
@endsection
