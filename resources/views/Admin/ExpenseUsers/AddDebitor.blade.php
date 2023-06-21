@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($agent) ? 'Edit Debitor' : 'Add Debitor' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-body">
                <form action="{{ isset($agent) ? url('expense-users/debitors/edit') : url('expense-users/debitors/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="userId" value="{{ isset($agent) ? $agent->id : '' }}">
                        <input type="hidden" name="role" value="agent">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Creditor Name <span style="color:red">*</span></label>
                                <input type="text" name="name" placeholder="John" class="form-control"
                                    data-validation="required" value="{{ isset($agent) ? $agent->name : old('name') }}">
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Phone <span style="color:red">*</span></label>
                                <input type="number" name="phone"
                                    value="{{ isset($agent) ? $agent->phone : old('phone') }}" id="phone"
                                    placeholder="972873818" data-errortext="This is dealer's username!" class="form-control"
                                    data-validation="required">
                                @error('phone')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email"
                                    value="{{ isset($agent) ? $agent->email : old('email') }}" id="username"
                                    placeholder="johs@gmail.com" class="form-control">
                                @error('email')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/expense-users/creditors') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
