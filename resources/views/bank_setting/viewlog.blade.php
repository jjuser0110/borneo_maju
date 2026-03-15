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
                            <a href="#" class="edit-stock-btn"
                                data-id="{{ $stock->id }}"
                                data-balance="{{ $stock->idr_balance }}"
                                data-bs-toggle="modal"
                                data-bs-target="#editStockModal">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            @if ($stock->idr_balance == $stock->idr_amount)
                                <a style="color:red;cursor:pointer"
                                    onclick="if(confirm('{{ __('sidebar.confirm_delete') }}')){showLoading();window.location.href='{{ route('bank_setting.destroy_stock', $stock) }}'}">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header flex-column flex-md-row">
            <div class="head-label mb-3">
                <h5 class="card-title mb-0">
                    {{ __('sidebar.bank_logs') }} <span style="color:green">IDR {{ number_format($bank_setting->amount ?? 0) }}</span>
                </h5>
            </div>
            <div class="col-12 mb-4">
                <form method="GET" onsubmit="showLoading()">
                    <div class="row g-2 align-items-end">

                        <div class="col-md-4">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date" value="{{ request('date') }}">
                        </div>

                        <div class="col-md-4">
                            <label>{{ __('sidebar.description') }}</label>
                            <select name="type[]" class="form-control select2" multiple>
                                <option value="stock_in" {{ in_array('stock_in', request('type', [])) ? 'selected' : '' }}>
                                    {{ __('sidebar.stock_in') }}
                                </option>
                                <option value="stock_out" {{ in_array('stock_out', request('type', [])) ? 'selected' : '' }}>
                                    {{ __('sidebar.stock_out') }}
                                </option>
                                <option value="stock_delete" {{ in_array('stock_delete', request('type', [])) ? 'selected' : '' }}>
                                    {{ __('sidebar.stock_delete') }}
                                </option>
                                <option value="stock_adjust_in" {{ in_array('stock_adjust_in', request('type', [])) ? 'selected' : '' }}>
                                    {{ __('sidebar.stock_adjust_in') }}
                                </option>
                                <option value="stock_adjust_out" {{ in_array('stock_adjust_out', request('type', [])) ? 'selected' : '' }}>
                                    {{ __('sidebar.stock_adjust_out') }}
                                </option>
                                <option value="transfer_in" {{ in_array('transfer_in', request('type', [])) ? 'selected' : '' }}>
                                    {{ __('sidebar.transfer_in') }}
                                </option>
                                <option value="transfer_out" {{ in_array('transfer_out', request('type', [])) ? 'selected' : '' }}>
                                    {{ __('sidebar.transfer_out') }}
                                </option>
                                <option value="expenses" {{ in_array('expenses', request('type', [])) ? 'selected' : '' }}>
                                    {{ __('sidebar.expenses') }}
                                </option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <button class="btn btn-primary w-100">Filter</button>
                        </div>

                    </div>
                </form>
            </div>
            {{-- <div class="dt-action-buttons text-end pt-3 pt-md-0">
                <div class="dt-buttons">
                    <button class="dt-button create-new btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#adjustAmountModal">
                        <span>
                            <span class="d-none d-sm-inline-block">
                                {{ __('sidebar.adjust_amount') }}
                            </span>
                        </span>
                    </button>
                </div>
            </div> --}}
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
                    @foreach($bank_logs as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ __('sidebar.' . $row->type ?? "") }}</td>
                        <td>{{ $row->remarks ?? "" }}</td>
                        <td style="text-align: right;">{{ number_format($row->prev_amount ?? 0, 2) }}</td>
                        <td style="text-align: right; color: {{ $row->signed_amount < 0 ? 'red' : 'green' }}">
                            {{ number_format($row->signed_amount) }}
                        </td>
                        <td style="text-align: right;">{{ number_format($row->after_amount ?? 0) }}</td>
                        <td>{{ $row->created_at ?? "" }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total:</th>
                        <th style="text-align: right;"></th> {{-- Amount total here --}}
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
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
                        <label class="form-label">{{ __('sidebar.description') }}</label>
                        <select name="type" class="form-control" required>
                            <option value="">{{ __('sidebar.select_type') }}</option>
                            <option value="stock_in">{{ __('sidebar.stock_in') }}</option>
                            <option value="transfer_in">{{ __('sidebar.transfer_in') }}</option>
                        </select>
                    </div>

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

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.remarks') }}</label>
                        <input type="text" name="remarks" class="form-control">
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

<div class="modal fade" id="editStockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_setting.update_stock_balance') }}" method="POST" id="editStockForm">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('sidebar.adjust_stock_balance') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <input type="hidden" name="stock_id" id="edit_stock_id">

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.current_balance') }}</label>
                        <input type="text" id="current_balance" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.description') }}</label>
                        <select name="type" class="form-control" required>
                            <option value="">{{ __('sidebar.select_type') }}</option>
                            <option value="stock_adjust">{{ __('sidebar.stock_adjust') }}</option>
                            <option value="transfer">{{ __('sidebar.transfer') }}</option>
                            <option value="expenses">{{ __('sidebar.expenses') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.amount') }}</label>
                        <input type="number" name="amount" id="edit_amount" class="form-control" step="0.01" required>
                        <small id="balance_error" class="text-danger d-none">
                            {{ __('sidebar.amount_exceeds_available_balance') }}
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.remaining_balance') }}</label>
                        <input type="text" id="remaining_balance" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('sidebar.remarks') }}</label>
                        <input type="text" name="remarks" class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ __('sidebar.close') }}
                    </button>
                    <button type="submit" class="btn btn-primary" id="edit_submit_btn">
                        {{ __('sidebar.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- <div class="modal fade" id="adjustAmountModal" tabindex="-1" aria-hidden="true">
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
</div> --}}
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
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>' +
             '<"table-responsive"t>' +
             '<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        pageLength: 10,
        ordering: false,

        footerCallback: function (row, data, start, end, display) {

            var api = this.api();

            var intVal = function (i) {
                return typeof i === 'string'
                    ? i.replace(/,/g, '') * 1
                    : typeof i === 'number'
                        ? i
                        : 0;
            };

            var total = api
                .column(4, { search: 'applied' }) // sum filtered rows only
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(4).footer()).html(
                total.toLocaleString()
            );
        }
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

    let currentBalance = 0;
    // When clicking edit icon
    $(document).on('click', '.edit-stock-btn', function () {
        let stockId = $(this).data('id');
        currentBalance = parseFloat($(this).data('balance'));

        $('#edit_stock_id').val(stockId);
        $('#current_balance').val(currentBalance.toLocaleString());
        $('#remaining_balance').val(currentBalance.toLocaleString());
        $('#edit_amount').val('');
        $('#balance_error').addClass('d-none');
        $('#edit_submit_btn').prop('disabled', false);
    });

    // When typing amount
    $(document).on('input', '#edit_amount', function () {

        let amount = parseFloat($(this).val()) || 0;
        let remaining = currentBalance - amount;

        if (amount > currentBalance) {
            $('#balance_error').removeClass('d-none');
            $('#edit_submit_btn').prop('disabled', true);
            $('#remaining_balance').val('0');
        } else {
            $('#balance_error').addClass('d-none');
            $('#edit_submit_btn').prop('disabled', false);
            $('#remaining_balance').val(remaining.toLocaleString());
        }
    });
</script>
@endsection
