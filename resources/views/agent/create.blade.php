@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a class="text-muted fw-light" href="{{route('agent.index')}}">Agent /</a> 
         @if (isset($agent)) Edit @else Create @endif
    </h4>
    <div class="row">
        <div class="col-12">
            <div class="card">
            <h5 class="card-header">Agent Details</h5>
            <div class="card-body">
                <form class="row g-3" enctype="multipart/form-data" @if (isset($agent)) method="post" action="{{ route('agent.update',$agent) }}" @else method="post" action="{{ route('agent.store') }}" @endif onsubmit="showLoading()">
                @csrf
                <div class="col-md-6">
                    <label class="form-label" for="agent_name">Agent Name</label>
                    <input
                    type="text"
                    class="form-control"
                    placeholder="Agent Name"
                    name="name"
                    value="{{$agent->name??''}}" 
                    required/>
                </div>
                @if(Auth::user()->role_id == 3)
                    <div class="col-md-6">
                        <label class="form-label" for="agent_username">Agent Username</label>
                        @if(isset($agent))
                            <input
                                type="text"
                                class="form-control"
                                placeholder="username"
                                name="username" 
                                value="{{$agent->username??''}}"
                                readonly
                            />
                        @else
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon11">{{Auth::user()->username??''}}</span>
                            <input type="text" class="form-control" name="username" placeholder="Username" aria-label="Username" aria-describedby="basic-addon11">
                        </div>
                        @endif
                    </div>
                @else
                <div class="col-md-6">
                    <label class="form-label" for="agent_username">Agent Username</label>
                    <input
                    type="text"
                    class="form-control"
                    placeholder="username"
                    name="username" 
                    value="{{$agent->username??''}}"
                    required
                    @if(isset($agent)) readonly @endif
                    />
                </div>
                @endif
                <div class="col-md-6">
                    <label class="form-label" for="password">Password</label>
                    <input
                    type="password"
                    class="form-control"
                    placeholder="password"
                    name="password"
                    @if(!isset($agent)) required @endif />
                </div>
                @if(!isset($agent))
                <div class="col-md-6">
                    <label class="form-label" for="point">Point <span class="badge bg-success">bal: {{Auth::user()->point??'unlimited'}}</span></label>
                    <input
                    type="number"
                    min="0"
                    max="{{Auth::user()->point??'9999999999999'}}"
                    class="form-control"
                    name="point"
                    value="{{$agent->point??''}}" 
                    required/>
                </div>
                @endif
                <div class="col-md-6">
                    <label class="form-label" for="idr_rate">IDR Rate <span class="badge bg-success">rate: {{Auth::user()->idr_rate??'unlimited'}}</span></label>
                    <input
                    type="number"
                    min="1"
                    max="{{Auth::user()->idr_rate??'9999999999999'}}"
                    class="form-control"
                    name="idr_rate"
                    value="{{$agent->idr_rate??''}}" 
                    required/>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="processing_fees">Processing Fees <span class="badge bg-success">fees: {{Auth::user()->processing_fees??'unlimited'}}</span></label>
                    <input
                    type="number"
                    min="{{Auth::user()->processing_fees??'1'}}"
                    class="form-control"
                    name="processing_fees"
                    value="{{$agent->processing_fees??''}}" 
                    required/>
                </div>
                @if(isset($agent))
                <div class="col-md-6">
                    <label class="form-label" for="password">Is Active?</label>
                    <select name="is_active" class="form-control">
                        <option value="1" <?php echo isset($agent)&&$agent->is_active == 1?'selected':'' ?>>Active</option>
                        <option value="0" <?php echo isset($agent)&&$agent->is_active == 0?'selected':'' ?>>Inactive</option>
                    </select>
                </div>
                @endif
                <hr>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->
@endsection

@section('scripts')
@endsection
