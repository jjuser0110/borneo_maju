@extends('layouts.app')
@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">{{ __('sidebar.point_logs') }}</span>
    </h4>

    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-header flex-column flex-md-row">
            <div class="head-label">
                <h5 class="card-title mb-0">{{ __('sidebar.my_point_logs') }}</h5>
            </div>
        </div>
        <div class="card-datatable text-nowrap">
            <table class="dt-column-search table table-bordered" id="mytable">
                <thead>
                    <tr>
                        <th>{{ __('sidebar.description') }}</th>
                        <th>{{ __('sidebar.before') }}</th>
                        <th>{{ __('sidebar.points') }}</th>
                        <th>{{ __('sidebar.after') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(Auth::user()->point_history as $row)
                    <tr>
                        <td>{{ $row->description ?? "" }}</td>
                        <td>{{ number_format($row->point_before ?? 0, 2) }}</td>
                        @if($row->point_after > $row->point_before)
                            <td style="color:green">+{{ number_format($row->point ?? 0, 2) }}</td>
                        @else
                            <td style="color:red">-{{ number_format(abs($row->point ?? 0), 2) }}</td>
                        @endif
                        <td>{{ number_format($row->point_after ?? 0, 2) }}</td>
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
    ordering: false,
    lengthMenu: [5, 10, 25, 50, 75, 100],
  });
});
</script>
@endsection
