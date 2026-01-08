@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4"><span class="text-muted fw-light">Sales Report</span></h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label">
                    <h5 class="card-title mb-0">My Downlines</h5>
                </div>
                
                <div class="col-md-6 col-12 mb-4">
                    <form method="GET">
                        <div class="input-group input-daterange" >
                            <input type="date" class="form-control" name="date_from" value="{{$date_from??''}}"/>
                            <span class="input-group-text">to</span>
                            <input type="date" class="form-control" name="date_to" value="{{$date_to??''}}"/>
                            <button class="btn btn-primary" type="submit" >Filter</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="dt-column-search table table-bordered" id="mytable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Total Order</th>
                            <th>Total IDR</th>
                            <th>Total MYR</th>
                            <th>Total Processing Fees</th>
                            <th>Total Do Up</th>
                            <th>Total Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $index=> $row)
                        <tr>
                            <td>{{$index+1}}</td>
                            <td><a href="{{route('report.sales_report',['user_id'=>$row->id,'date_from'=>$date_from,'date_to'=>$date_to])}}">{{$row->username??""}}</a></td>
                            <td>{{$row->name??""}}</td>
                            <td>{{$row->total_order??0}}</td>
                            <td>IDR {{number_format($row->total_idr??0,2)}}</td>
                            <td>MYR {{number_format($row->total_myr??0,2)}}</td>
                            <td>MYR {{number_format($row->total_processing_fees??0,2)}}</td>
                            <td>MYR {{number_format($row->total_do_up??0,2)}}</td>
                            <td>MYR {{number_format($row->total_profit??0,2)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total:</th>
                            <th></th>
                            <th></th>
                            <th>{{$total_order??0}}</th>
                            <th>IDR {{number_format($agents->sum('total_idr')??0,2)}}</th>
                            <th>MYR {{number_format($agents->sum('total_myr')??0,2)}}</th>
                            <th>MYR {{number_format($agents->sum('total_processing_fees')??0,2)}}</th>
                            <th>MYR {{number_format($agents->sum('total_do_up')??0,2)}}</th>
                            <th>MYR {{number_format($agents->sum('total_profit')??0,2)}}</th>
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