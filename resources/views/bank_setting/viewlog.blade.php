@extends('layouts.app')

@section('content')
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">{{ __('sidebar.bank_setting') }} ({{ $bank_setting->owner_name ?? '' }})</span>
    </h4>

    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-header flex-column flex-md-row">
            <div class="head-label">
                <h5 class="card-title mb-0">
                    {{ __('sidebar.bank_logs') }} <span style="color:green">{{ $bank_setting->amount ?? 0 }}</span>
                </h5>
            </div>
            <div class="dt-action-buttons text-end pt-3 pt-md-0">
                <div class="dt-buttons">
                    <button class="dt-button create-new btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#adjustAmountModal">
                        <span>
                            <i class="bx bx-plus me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">
                                {{ __('sidebar.adjust_amount') }}
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
                        <td>{{ $row->prev_amount ?? 0 }}</td>
                        <td @if($row->after_amount > $row->prev_amount) style="color:green"
                            @elseif($row->after_amount < $row->prev_amount) style="color:red"
                            @endif>{{ $row->amount ?? 0 }}</td>
                        <td>{{ $row->after_amount ?? 0 }}</td>
                        <td>{{ $row->created_at ?? "" }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- / Content -->

<div class="modal fade" id="adjustAmountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('bank_setting.adjust_money') }}" method="POST">
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
                    <button type="submit" class="btn btn-primary" onclick="showLoading()">
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
});
</script>
@endsection
