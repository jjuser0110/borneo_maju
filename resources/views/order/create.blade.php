@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{route('order.index')}}">Order /</a> 
         @if (isset($order)) Edit @else Create @endif
    </h4>
    <div class="row">
        <div class="col-12">
            <div class="card">
            <h5 class="card-header">Order Details</h5>
            <div class="card-body">
                <form class="row g-3" enctype="multipart/form-data" @if (isset($order)) method="post" action="{{ route('order.update',$order) }}" @else method="post" action="{{ route('order.store') }}" @endif onsubmit="showLoading()">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Order No.</label>
                    <input
                        type="text"
                        class="form-control"
                        name="order_no"
                        value="{{ $order->order_no ?? $order_no ?? '' }}"
                        required
                        readonly
                    />
                    <input type="hidden" name="code" value="{{ $code ?? '' }}">
                    <input type="hidden" name="year" value="{{ $year ?? '' }}">
                    <input type="hidden" name="month" value="{{ $month ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Order Date</label>
                    <input
                        type="date"
                        class="form-control"
                        name="order_datetime"
                        value="{{ $order->order_datetime ?? date('Y-m-d') }}"
                        required
                        @if(isset($order)) readonly @endif
                    />
                </div>
                <div class="col-md-4 mb-4">
                    <label for="select2Basic" class="form-label">Bank</label>
                    <select id="select2Basic" name="bank_id" class="select2 form-select" data-allow-clear="true" required>
                        @foreach($bank as $bank)
                            <option value="{{$bank->id}}" @if(isset($order) && $order->bank_id == $bank->id) selected @endif>{{$bank->bank_name??''}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Account No.</label>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="82728191098282"
                        name="account_no"
                        value="{{ $order->account_no ?? Auth::user()->account_no ?? '' }}"
                        required
                    />
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fullname</label>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="John Doe"
                        name="fullname"
                        value="{{ $order->fullname ?? Auth::user()->fullname ?? '' }}"
                        required
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">IDR Rate</label>
                    <input
                        type="number"
                        class="form-control"
                        name="idr_rate"
                        id="idr_rate"
                        min="0"
                        max="{{Auth::user()->idr_rate ?? 99999999}}"
                        value="{{ $order->idr_rate ?? Auth::user()->idr_rate ?? '' }}"
                        required
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">MYR AMOUNT</label>
                    <input
                        type="number"
                        class="form-control"
                        min="0"
                        step="0.01"
                        name="myr_amount"
                        id="myr_amount"
                        value="{{ $order->myr_amount ?? '' }}"
                        required
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">IDR AMOUNT</label>
                    <input
                        type="number"
                        class="form-control"
                        name="idr_amount"
                        id="idr_amount"
                        min="0"
                        value="{{ $order->idr_amount ?? '' }}"
                        required
                    />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Processing Fees</label>
                    <input
                        type="number"
                        class="form-control"
                        name="processing_fees"
                        min ="{{Auth::user()->processing_fees ?? 0}}"
                        step="0.01"
                        value="{{ $order->processing_fees ?? Auth::user()->processing_fees ?? '' }}"
                        required
                    />
                </div>
                <hr>
                <div class="col-12">
                    <button type="submit" name="submitButton" class="btn btn-primary">Submit</button>
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
        idrInput.value = round(myr * rate, 2);
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
        idrInput.value = round(myr * rate, 2);
        isUpdating = false;
    });
</script>
@endsection

