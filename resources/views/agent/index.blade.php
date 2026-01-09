@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4">
            <span class="text-muted fw-light">{{ __('sidebar.agent') }}</span>
        </h4>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label">
                    <h5 class="card-title mb-0">{{ __('sidebar.agent_listing') }}</h5>
                </div>
                <div class="dt-action-buttons text-end pt-3 pt-md-0">
                    <div class="dt-buttons"> 
                        <a class="dt-button create-new btn btn-primary"
                           type="button"
                           href="{{ route('agent.create') }}"
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
                            <th>{{ __('sidebar.no') }}</th>
                            <th>{{ __('sidebar.username') }}</th>
                            <th>{{ __('sidebar.name') }}</th>
                            <th>{{ __('sidebar.role') }}</th>
                            <th>{{ __('sidebar.point') }}</th>
                            <th>{{ __('sidebar.idr_rate') }}</th>
                            <th>{{ __('sidebar.processing_fees') }}</th>
                            <th>{{ __('sidebar.status') }}</th>
                            <th>{{ __('sidebar.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agent as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('agent.downline', $row) }}">
                                    {{ $row->username ?? "" }}
                                </a>
                            </td>
                            <td>{{ $row->name ?? "" }}</td>
                            <td>{{ $row->role->title ?? "" }}</td>
                            <td>{{ $row->point ?? "" }}</td>
                            <td>{{ $row->idr_rate ?? "" }}</td>
                            <td>{{ $row->processing_fees ?? "" }}</td>
                            <td>
                                {!! isset($row) && $row->is_active == 1 
                                    ? '<span style="color:green">' . __('sidebar.active') . '</span>'
                                    : '<span style="color:red">' . __('sidebar.inactive') . '</span>' !!}
                            </td>
                            <td>
                                <a href="{{ route('agent.viewlog', $row) }}" onclick="showLoading()">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </a>
                                <a href="{{ route('agent.edit', $row) }}" onclick="showLoading()">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
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
