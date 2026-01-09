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
                    <h5 class="card-title mb-0">{{ __('sidebar.downline_list') }}</h5>
                </div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="dt-column-search table table-bordered" id="mytable">
                    <thead>
                        <tr>
                            <th>{{ __('sidebar.no') }}</th>
                            <th>{{ __('sidebar.username') }}</th>
                            <th>{{ __('sidebar.name') }}</th>
                            <th>{{ __('sidebar.role') }}</th>
                            <th>{{ __('sidebar.point') }}</th>
                            <th>{{ __('sidebar.idr_rate') }}</th>
                            <th>{{ __('sidebar.processing_fees') }}</th>
                            <th>{{ __('sidebar.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agent->downlines as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><a href="{{ route('agent.downline', $row) }}">{{ $row->username ?? '' }}</a></td>
                            <td>{{ $row->name ?? '' }}</td>
                            <td>{{ $row->role->title ?? '' }}</td>
                            <td>{{ $row->point ?? '' }}</td>
                            <td>{{ $row->idr_rate ?? '' }}</td>
                            <td>{{ $row->processing_fees ?? '' }}</td>
                            <td>
                                @if(isset($row) && $row->is_active == 1)
                                    <span style="color:green">{{ __('sidebar.active') }}</span>
                                @else
                                    <span style="color:red">{{ __('sidebar.inactive') }}</span>
                                @endif
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
