@extends('Admin.index')
@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{isset($franchise)?"Edit Franchise":"Add Franchise"}}</h1>
                <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
            </div>
        </div>
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
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{isset($franchise)?url('franchises/edit'):url('franchises/add')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{isset($franchise)?$franchise->id:''}}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Name<span style="color:red">*</span></label>
                                <input  type="text" name="name" placeholder="Franchise Name"
                                    class="form-control"  value="{{isset($franchise)?$franchise->name:old('name')}}">
                                    @error('name')
                                    <span class="text-danger">
                                        {{$message}}
                                    </span>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Monthly Target <span style="color:red">*</span></label>
                                <input type="number" name="monthly_target" value="{{isset($franchise)?$franchise->monthly_target:old('monthly_target')}}" id="phone" placeholder="100"
                                     class="form-control"
                                    data-validation="required">
                                    @error('monthly_target')
                                    <span class="text-danger">
                                        {{$message}}
                                    </span>
                                    @enderror
                                        
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Number of users <span style="color:red">*</span></label>
                                <input type="number" name="users_count" value="{{isset($franchise)?$franchise->users_count:old('users_count')}}" id="phone" placeholder="100"
                                     class="form-control"
                                    data-validation="required">
                                    @error('users_count')
                                    <span class="text-danger">
                                        {{$message}}
                                    </span>
                                    @enderror
                                        
                            </div>
                        </div>



                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{url('/franchises')}}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
