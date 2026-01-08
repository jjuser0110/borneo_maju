@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4"><span class="text-muted fw-light">Agent </span></h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label">
                    <h5 class="card-title mb-0">Agent Listing</h5>
                </div>
                <div class="dt-action-buttons text-end pt-3 pt-md-0">
                    <div class="dt-buttons"> 
                        <a class="dt-button create-new btn btn-primary" type="button" href="{{route('agent.create')}}" onclick="showLoading()">
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
                            <th>Username</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Point</th>
                            <th>IDR RATE</th>
                            <th>Processing Fees</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agent as $index=> $row)
                        <tr>
                            <td>{{$index+1}}</td>
                            <td><a href="{{route('agent.downline',$row)}}">{{$row->username??""}}</a></td>
                            <td>{{$row->name??""}}</td>
                            <td>{{$row->role->title??""}}</td>
                            <td>{{$row->point??""}}</td>
                            <td>{{$row->idr_rate??""}}</td>
                            <td>{{$row->processing_fees??""}}</td>
                            <td><?php echo isset($row)&&$row->is_active == 1?'<span style="color:green">Active</span>':'<span style="color:red">Inactive</span>'?></td>
                            <td>
                                <a href="{{route('agent.viewlog',$row)}}" onclick="showLoading()"><i class="fa-solid fa-clock-rotate-left"></i></a>
                                <a href="{{ route('agent.edit',$row) }}" onclick="showLoading()"><i class="fa-solid fa-pen-to-square"></i></a>
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
        responsive: true,
        pageLength: 10,
        displayLength: 7,
        lengthMenu: [7, 10, 25, 50, 75, 100],
      });
    });
  </script>
    @endsection