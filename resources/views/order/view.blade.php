@extends('layouts.app')
@section('content')
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="py-3 breadcrumb-wrapper mb-4"><span class="">{{$order->order_no??''}} </span></h4>

        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6">
                <!-- About User -->
                <div class="card mb-4">
                    <div class="card-body">
                        <p class="card-text text-uppercase">Details</p>
                        <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">Order No:</span>
                            <span>{{$order->order_no??''}}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-star bx-xs"></i><span class="fw-medium mx-2">Order Date:</span>
                            <span>{{$order->order_datetime??''}}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-check bx-xs"></i><span class="fw-medium mx-2">Status:</span>
                            <span>{{$order->status??''}}</span>
                        </li>
                        </ul>
                        <p class="card-text text-uppercase">Banks</p>
                        <ul class="list-unstyled mb-4">
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-flag bx-xs"></i><span class="fw-medium mx-2">Bank:</span> <span>{{$order->bank->bank_name??''}}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-detail bx-xs"></i><span class="fw-medium mx-2">Account No:</span>
                            <span>{{$order->account_no??''}}</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-detail bx-xs"></i><span class="fw-medium mx-2">Fullname:</span>
                            <span>{{$order->fullname??''}}</span>
                        </li>
                        </ul>
                        <small class="text-muted text-uppercase">Teams</small>
                        <ul class="list-unstyled mt-3 mb-0">
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bxl-github text-primary me-2"></i>
                            <div class="d-flex flex-wrap">
                            <span class="fw-medium me-2">Backend Developer</span><span>(126 Members)</span>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bx bxl-react text-info me-2"></i>
                            <div class="d-flex flex-wrap">
                            <span class="fw-medium me-2">React Developer</span><span>(98 Members)</span>
                            </div>
                        </li>
                        </ul>
                    </div>
                </div>
                <!--/ About User -->
                <!-- Profile Overview -->
                <div class="card mb-4">
                    <div class="card-body">
                        <p class="card-text text-uppercase">Overview</p>
                        <ul class="list-unstyled mb-0">
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-check bx-xs"></i><span class="fw-medium mx-2">Task Compiled:</span>
                            <span>13.5k</span>
                        </li>
                        <li class="d-flex align-items-center mb-3">
                            <i class="bx bx-star bx-xs"></i><span class="fw-medium mx-2">Projects Compiled:</span>
                            <span>146</span>
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">Connections:</span>
                            <span>897</span>
                        </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->


    @endsection
    @section('page-js')
    @endsection
    @section('scripts')
    @endsection