@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4">
            <span class="text-muted fw-light">{{ __('sidebar.orders') }}</span>
        </h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label">
                    <h5 class="card-title mb-0">{{ __('sidebar.order_listing') }}</h5>
                </div>

                <div class="col-md-6 col-12 mb-4">
                    <form method="GET">
                        <div class="input-group input-daterange">
                            <input type="date" class="form-control" name="date_from" value="{{ $date_from ?? '' }}"/>
                            <span class="input-group-text">{{ __('sidebar.to') }}</span>
                            <input type="date" class="form-control" name="date_to" value="{{ $date_to ?? '' }}"/>
                            @if(Auth::user()->role_id != 3)
                            <select name="user_id" class="form-select">
                                <option value="">{{ __('sidebar.all_agents') }}</option>
                                @foreach($agent as $row)
                                    <option value="{{ $row->id }}" {{ (isset($user_id) && $user_id == $row->id) ? 'selected' : '' }}>{{ $row->username }}</option>
                                @endforeach
                            </select>
                            @endif
                            <button class="btn btn-primary" type="submit">{{ __('sidebar.filter') }}</button>
                        </div>
                    </form>
                </div>

                <div class="dt-action-buttons text-end pt-3 pt-md-0">
                    <div class="dt-buttons">
                        <a class="dt-button create-new btn btn-primary" type="button" href="{{route('order.create')}}" onclick="showLoading()">
                            <span><i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">{{ __('sidebar.add_new_record') }}</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-datatable text-nowrap">
                <table class="dt-column-search table table-bordered" id="mytable">
                    <thead>
                        <tr>
                            <th>{{ __('sidebar.no') }}</th>
                            <th>{{ __('sidebar.order_no') }}</th>
                            <th>{{ __('sidebar.user') }}</th>
                            <th>{{ __('sidebar.order_date') }}</th>
                            <th>{{ __('sidebar.bank') }}</th>
                            <th>{{ __('sidebar.account_no') }}</th>
                            <th>{{ __('sidebar.fullname') }}</th>
                            <th>{{ __('sidebar.idr_rate') }}</th>
                            <th>{{ __('sidebar.myr_amount') }}</th>
                            <th>{{ __('sidebar.idr_amount') }}</th>
                            <th>{{ __('sidebar.processing_fees') }}</th>
                            <th>{{ __('sidebar.total_amount') }}</th>
                            <th>{{ __('sidebar.status') }}</th>
                            <th>{{ __('sidebar.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order as $index=>$row)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$row->order_no??""}}</td>
                            <td>{{$row->user->username??""}}</td>
                            <td>{{$row->order_datetime??""}}</td>
                            <td>{{$row->bank->bank_name??""}}</td>
                            <td>{{$row->account_no??""}}</td>
                            <td>{{$row->fullname??""}}</td>
                            <td style="text-align:center">{{$row->idr_rate??""}}</td>
                            <td style="text-align:right">{{number_format($row->myr_amount, 2)}}</td>
                            <td style="text-align:right">{{number_format($row->idr_amount)}}</td>
                            <td style="text-align:right">{{number_format($row->processing_fees, 2)}}</td>
                            <td style="text-align:right">{{number_format($row->total_amount, 2)}}</td>
                            <td>{{$row->status??""}}</td>
                            <td>
                                <a href="{{ route('order.view_details',$row) }}" onclick="showLoading()"><i class="fa-solid fa-eye"></i></a>
                                @if (Auth::user()->id === $row->user_id && $row->status == 'pending')
                                    <a href="{{ route('order.edit',$row) }}" onclick="showLoading()"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a style="color:red;cursor:pointer" onclick="if(confirm('{{ __('sidebar.confirm_delete') }}')){showLoading();window.location.href='{{ route('order.destroy',$row) }}'}"><i class="fa-solid fa-trash"></i></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="8" style="text-align:right">Total</th>
                            <th style="text-align:right">{{number_format($order->sum('myr_amount'), 2)}}</th>
                            <th style="text-align:right">{{number_format($order->sum('idr_amount'))}}</th>
                            <th style="text-align:right">{{number_format($order->sum('processing_fees'), 2)}}</th>
                            <th style="text-align:right">{{number_format($order->sum('total_amount'), 2)}}</th>
                            <th colspan="2"></th>
                        </tr>
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
