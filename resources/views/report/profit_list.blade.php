@extends('layouts.app')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4">
            <span class="text-muted fw-light">Report /</span> Profit List
        </h4>

        <div class="card mb-3">
            <div class="card-header flex-column flex-md-row d-flex justify-content-between align-items-center">
                <div class="head-label">
                    <h5 class="card-title mb-0">Profit List ({{ $date_from }} to {{ $date_to }})</h5>
                </div>
                <div>
                    <a href="{{ route('report.daily_report', ['date_from' => $date_from, 'date_to' => $date_to]) }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Daily Report
                    </a>
                </div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="dt-column-search table table-bordered" id="mytable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order No</th>
                            <th>Completed At</th>
                            <th>Amount Received (MYR)</th>
                            <th>Capital Used (MYR)</th>
                            <th>Profit (MYR)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profits as $index => $item)
                            <tr>
                                <td>{{ $profits->firstItem() + $index }}</td>
                                <td>{{ $item->order->order_no ?? '-' }}</td>
                                <td>{{ $item->order->status_at ?? '-' }}</td>
                                <td class="text-end">{{ number_format($item->amount_received, 2) }}</td>
                                <td class="text-end">{{ number_format($item->capital_used, 2) }}</td>
                                <td class="text-end text-success fw-bold">{{ number_format($item->profit, 2) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('order.view_details', $item->order_id) }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No profit records found for this date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($profits->total() > 0)
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">{{ number_format($totals['amount_received'], 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($totals['capital_used'], 2) }}</td>
                                <td class="text-end fw-bold text-success">{{ number_format($totals['profit'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <!-- Pagination Links -->
            @if($profits->hasPages())
                <div class="card-footer d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <div class="mb-2 mb-md-0 text-muted">
                        Showing {{ $profits->firstItem() }} to {{ $profits->lastItem() }} of {{ $profits->total() }} entries
                    </div>
                    <div>
                        <nav aria-label="Page navigation">
                            {{ $profits->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
