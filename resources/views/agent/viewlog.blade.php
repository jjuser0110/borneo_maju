@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4">
            <span class="text-muted fw-light">{{ __('sidebar.agent') }} ({{ $agent->username ?? '' }})</span>
        </h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label">
                    <h5 class="card-title mb-0">
                        {{ __('sidebar.point_logs') }} <span style="color:green">{{ $agent->point ?? 0 }}</span>
                    </h5>
                </div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="dt-column-search table table-bordered" id="mytable">
                    <thead>
                        <tr>
                            <th>{{ __('sidebar.no') }}</th>
                            <th>{{ __('sidebar.description') }}</th>
                            <th>{{ __('sidebar.before') }}</th>
                            <th>{{ __('sidebar.point') }}</th>
                            <th>{{ __('sidebar.after') }}</th>
                            <th>{{ __('sidebar.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agent->point_history as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->description ?? "" }}</td>
                            <td>{{ $row->point_before ?? 0 }}</td>
                            <td @if($row->point_after > $row->point_before) style="color:green" 
                                @elseif($row->point_after < $row->point_before) style="color:red" 
                                @endif>{{ $row->point ?? 0 }}</td>
                            <td>{{ $row->point_after ?? 0 }}</td>   
                            <td>{{ $row->created_at ?? "" }}</td>
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
