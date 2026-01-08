@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4"><span class="text-muted fw-light">Agent ({{$agent->username??''}})</span></h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label">
                    <h5 class="card-title mb-0">Point Logs <span style="color:green">{{$agent->point??0}}</span></h5>
                </div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="dt-column-search table table-bordered" id="mytable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Descriptions</th>
                            <th>Before</th>
                            <th>Point</th>
                            <th>After</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agent->point_history as $index=> $row)
                        <tr>
                            <td>{{$index+1}}</td>
                            <td>{{$row->description??""}}</td>
                            <td>{{$row->point_before??0}}</td>
                            <td <?php if($row->point_after > $row->point_before) { echo 'style="color:green"'; } elseif($row->point_after < $row->point_before) { echo 'style="color:red"'; } ?>>{{$row->point??0}}</td>
                            <td>{{$row->point_after??0}}</td>   
                            <td>{{$row->created_at??""}}</td>
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