@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4"><span class="text-muted fw-light">Order </span></h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label">
                    <h5 class="card-title mb-0">Order Listing</h5>
                </div>
                <div class="dt-action-buttons text-end pt-3 pt-md-0">
                    <div class="dt-buttons"> 
                        <a class="dt-button create-new btn btn-primary" type="button" href="{{route('order.create')}}" onclick="showLoading()">
                            <span><i class="bx bx-plus me-sm-1"></i> 
                                <span class="d-none d-sm-inline-block">Add New Record</span>
                            </span>
                        </a> 
                    </div>
                </div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="dt-column-search table table-bordered" id="mytable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Order No</th>
                            <th>Order Date</th>
                            <th>Bank</th>
                            <th>Account No</th>
                            <th>Fullname</th>
                            <th>IDR Rate</th>
                            <th>MYR Amount</th>
                            <th>IDR Amount</th>
                            <th>Processing Fees</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order as $index=>$row)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$row->order_no??""}}</td>
                            <td>{{$row->order_datetime??""}}</td>
                            <td>{{$row->bank->bank_name??""}}</td>
                            <td>{{$row->account_no??""}}</td>
                            <td>{{$row->fullname??""}}</td>
                            <td>{{$row->idr_rate??""}}</td>
                            <td>{{$row->myr_amount??""}}</td>
                            <td>{{$row->idr_amount??""}}</td>
                            <td>{{$row->processing_fees??""}}</td>
                            <td>{{$row->total_amount??""}}</td>
                            <td>{{$row->status??""}}</td>
                            <td>
                                <a href="{{ route('order.edit',$row) }}" onclick="showLoading()"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a style="color:red;cursor:pointer" onclick="if(confirm('Are you sure you want to delete?')){showLoading();window.location.href='{{ route('order.destroy',$row) }}'}"><i class="fa-solid fa-trash"></i></a>
                                <div class="btn-group">
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