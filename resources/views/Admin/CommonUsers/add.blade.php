@extends('Admin.index')
@section('content')
<section class="content-header">
    <div class="container-fluid">
        
        @if (session()->has('msg-success'))
            <div class="alert alert-success" role="alert">
                {{ session('msg-success') }}
            </div>
        @elseif (session()->has('msg-error'))
            <div class="alert alert-danger" role="alert">
                {{ session('msg-success') }}
            </div>
        @endif
    </div>
</section>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($user) ? 'Edit ' . $heading : 'Add ' . $heading }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{ isset($user) ? url($route.'/edit') : url($route.'/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="userId" value="{{ isset($user) ? $user->id : '' }}">
                        <input type="hidden" name="role" value="{{$role}}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Name <span style="color:red">*</span></label>
                                <input type="text" name="name" placeholder="John" class="form-control"
                                    data-validation="required" value="{{ isset($user) ? $user->name : old('name') }}">
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
                                    value="{{ isset($user) ? $user->phone : old('phone') }}" id="phone"
                                      class="form-control"
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
                                <label>Email <span style="color:red">*</span></label>
                                <input type="email" name="email"
                                    value="{{ isset($user) ? $user->email : old('email') }}" id="username"
                                    placeholder="johs@gmail.com" class="form-control">
                                @error('email')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Franchises</label>
                                <select id="" class="form-control" name="franchises_id">
                                    <option value="">--Choose</option>
                                    @foreach ($franchises as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>


                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Password <span style="color:red">*</span></label>
                                <input type="text" name="password" value="" id="password" placeholder="Password"
                                    class="form-control" data-validation="required">
                                @error('password')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Confirm Password <span style="color:red">*</span></label>
                                <input type="password" name="confirmPassword" value="" id="confirmPassword"
                                    placeholder="Confirm password" data-errortext="This is dealer's username!"
                                    class="form-control" data-validation="required">
                                @error('confirmPassword')
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
                            <a href="{{ url($route) }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
