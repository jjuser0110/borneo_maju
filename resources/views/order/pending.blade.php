@extends('layouts.app')
@section('content')
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
                            <td>{{$row->order_datetime??""}}</td>
                            <td>{{$row->bank->bank_name??""}}</td>
                            <td>{{$row->idr_amount??""}}</td>
                            <td>{{$row->status??""}}</td>
                            <td>
                                <a class="btn btn-primary" href="{{ route('order.view',$row) }}">{{ __('sidebar.handle') }}</a>
                            </td>
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
