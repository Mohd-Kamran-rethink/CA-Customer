@extends('Admin.index')
@section('content')

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{isset($exchange)?"Edit Franchise":"Add Franchise"}}</h1>
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
                <form action="{{isset($exchange)?url('exchanges/edit'):url('exchanges/add')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{isset($exchange)?$exchange->id:''}}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Name<span style="color:red">*</span></label>
                                <input  type="text" name="name" placeholder="Franchise Name"
                                    class="form-control"  value="{{isset($exchange)?$exchange->name:old('name')}}">
                                    @error('name')
                                    <span class="text-danger">
                                        {{$message}}
                                    </span>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Total Coins <span style="color:red">*</span></label>
                                <input step="any" type="number" name="total_coins" value="{{isset($exchange)?$exchange->total_coins:old('total_coins')}}" id="phone"
                                     class="form-control"
                                    data-validation="required">
                                    @error('total_coins')
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
                            <a href="{{url('/exchanges')}}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
