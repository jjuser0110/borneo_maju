@extends('layouts.app')
@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">{{ __('sidebar.bank_setting') }}</span>
    </h4>

    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-header flex-column flex-md-row">
            <div class="head-label">
                <h5 class="card-title mb-0">{{ __('sidebar.bank_setting') }}</h5>
            </div>
            <div class="dt-action-buttons text-end pt-3 pt-md-0">
                <div class="dt-buttons">
                    <a class="dt-button create-new btn btn-primary"
                        type="button"
                        href="{{ route('bank_setting.create') }}"
                        onclick="showLoading()">
                        <span>
                            <i class="bx bx-plus me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">
                                {{ __('sidebar.add_new_record') }}
                            </span>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="card-datatable text-nowrap">
            <table class="dt-column-search table table-bordered" id="mytable">
                <thead>
                    <tr>
                        <th>{{ __('sidebar.bank_name') }}</th>
                        <th>{{ __('sidebar.account_no') }}</th>
                        <th>{{ __('sidebar.owner_name') }}</th>
                        <th>{{ __('sidebar.amount') }}</th>
                        <th>{{ __('sidebar.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bank_setting as $row)
                    <tr>
                        <td>{{ $row->bank->bank_name ?? "" }}</td>
                        <td>{{ $row->account_no ?? "" }}</td>
                        <td>{{ $row->owner_name ?? "" }}</td>
                        <td>{{ number_format($row->amount,2) ?? "0.00" }}</td>
                        <td>
                            <a href="{{ route('bank_setting.viewlog', $row) }}" onclick="showLoading()">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </a>
                            <a style="color:green;cursor:pointer" onclick="openMoneyModal({{ $row->id }})">
                                <i class="fa-solid fa-money-bill"></i>
                            </a>
                            <a href="{{ route('bank_setting.edit', $row) }}" onclick="showLoading()">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a style="color:red;cursor:pointer"
                                onclick="if(confirm('{{ __('sidebar.confirm_delete') }}')){showLoading();window.location.href='{{ route('bank_setting.destroy',$row) }}'}">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- / Content -->
<div class="modal fade" id="adjustMoney" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form enctype="multipart/form-data" method="post" action="{{ route('bank_setting.adjust_money') }}" onsubmit="showLoading()">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">
                        {{ __('sidebar.adjust_amount') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('sidebar.close') }}"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="bank_setting_id" id="bank_setting_id">
                        <div class="col mb-3">
                            <label class="col-form-label">{{ __('sidebar.amount') }} </label>
                            <input class="form-control" type="number" name="amount" placeholder="{{ __('sidebar.amount') }}" autocomplete="false" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <label class="col-form-label">{{ __('sidebar.remarks') }}</label>
                            <input class="form-control" type="text" name="remarks" placeholder="{{ __('sidebar.remarks') }}" autocomplete="false" required>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
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
});

function openMoneyModal(id){
    $('#adjustMoney').modal('show');
    $('#bank_setting_id').val(id);
}
</script>
@endsection
