@extends('layouts.app')
@section('content')
<!-- Content -->
<style>
.drag-handle {
    cursor: move;
}
</style>
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
                        <th>#</th>
                        <th>{{ __('sidebar.bank_name') }}</th>
                        <th>{{ __('sidebar.account_no') }}</th>
                        <th>{{ __('sidebar.owner_name') }}</th>
                        <th>{{ __('sidebar.amount') }}</th>
                        <th>{{ __('sidebar.actions') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bank_setting as $row)
                    <tr data-id="{{ $row->id }}">
                        <td>{{ $row->position }}</td>
                        <td>{{ $row->bank->bank_name ?? "" }}</td>
                        <td>{{ $row->account_no ?? "" }}</td>
                        <td>{{ $row->owner_name ?? "" }}</td>
                        <td>{{ number_format($row->amount) }}</td>
                        <td>
                            <a href="{{ route('bank_setting.viewlog', $row) }}" onclick="showLoading()">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </a>
                            {{-- <a style="color:green;cursor:pointer" onclick="openMoneyModal({{ $row->id }})">
                                <i class="fa-solid fa-money-bill"></i>
                            </a> --}}
                            <a href="{{ route('bank_setting.edit', $row) }}" onclick="showLoading()">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a style="color:red;cursor:pointer"
                                onclick="if(confirm('{{ __('sidebar.confirm_delete') }}')){showLoading();window.location.href='{{ route('bank_setting.destroy',$row) }}'}">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                        <td><i class="fa fa-bars drag-handle"></i></td>
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
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Order position updated successfully
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    <div id="errorToast" class="toast align-items-center text-bg-danger border-0 mt-2" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Order position update failed
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@section('page-js')
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function(){
    var table = $('#mytable').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>><"table-responsive"t><"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        // pageLength: 10,
        // displayLength: 5,
        paging: false,
        ordering:false,
        // lengthMenu: [5, 10, 25, 50, 75, 100],
    });

    $('#mytable tbody').sortable({
        handle: ".drag-handle",
        helper: function(e, tr) {
            var originals = tr.children();
            var helper = tr.clone();
            helper.children().each(function(index) {
                $(this).width(originals.eq(index).width());
            });
            return helper;
        },
        stop: function(event, ui) {
            updateOrder();
        }
    });

    function updateOrder(){
        var order = [];

        $('#mytable tbody tr').each(function(index){
             let position = index + 1;

            $(this).find('td:first').text(position);

            order.push({
                id: $(this).data('id'),
                position: position
            });
        });

        $.ajax({
            url: "{{ route('bank_setting.update_order') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order: order
            },
            success: function(response){
                var toast = new bootstrap.Toast(document.getElementById('successToast'));
                toast.show();
            },
            error: function(){
                var toast = new bootstrap.Toast(document.getElementById('errorToast'));
                toast.show();
            }
        });
    }
});

function openMoneyModal(id){
    $('#adjustMoney').modal('show');
    $('#bank_setting_id').val(id);
}
</script>
@endsection
