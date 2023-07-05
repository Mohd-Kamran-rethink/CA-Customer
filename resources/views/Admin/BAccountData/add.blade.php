@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($bank) ? 'Edit Bank' : 'Add Bank' }}</h1>
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
                <form action="{{ isset($bank) ? url('bank-accounts/edit') : url('bank-accounts/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{ isset($bank) ? $bank->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Account Holder Name<span style="color:red">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ isset($bank) ? $bank->holder_name : old('name') }}">
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Bank Name<span style="color:red">*</span></label>
                                <input type="text" name="bank_name" class="form-control"
                                    value="{{ isset($bank) ? $bank->bank_name : old('bank_name') }}">
                                @error('bank_name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Account Number <span style="color:red">*</span></label>
                                <input type="number" name="account_number"
                                    value="{{ isset($bank) ? $bank->account_number : old('accout_number') }}" id="phone"
                                    class="form-control" data-validation="required">
                                @error('account_number')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>IFSC Code <span style="color:red">*</span></label>
                                <input type="text" name="ifcs_code"
                                    value="{{ isset($bank) ? $bank->ifsc : old('ifcs_code') }}" id="phone"
                                    class="form-control" data-validation="required">
                                @error('ifcs_code')
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
                                    value="{{ isset($bank) ? $bank->phone : old('phone') }}" id="phone"
                                    class="form-control" data-validation="required">
                                @error('phone')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Email </label>
                                <input type="email" name="email"
                                    value="{{ isset($bank) ? $bank->email : old('email') }}" id="email"
                                    class="form-control" data-validation="required">
                                @error('email')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Old Opening</label>
                                <input type="number" step="any" name="old_opening"
                                    value="{{ isset($bank) ? $bank->old_opening : old('old_opening') }}" id="email"
                                    class="form-control" data-validation="required">
                                @error('old_opening')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Initial Amount <span style="color:red">*</span> </label>
                                <input type="number" step="any" name="amount"
                                    value="{{ isset($bank) ? $bank->amount : old('amount') }}" id="email"
                                    class="form-control" data-validation="required">
                                @error('amount')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Bank Type<span style="color:red">*</span> </label>
                                <select class="form-control" data-validation="required" name="bank_type">
                                    <option value="0">--Choose--</option>
                                    <option {{isset($bank)&&$bank->type=="deposit"?'selected':''}} value="deposit">Deposit</option>
                                    <option {{isset($bank)&&$bank->type=="withdraw"?'selected':''}} value="withdraw">Withdraw</option>
                                    <option value="both">Both</option>
                                
                                </select>

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Providers<span style="color:red">*</span> </label>
                                <select class="form-control" data-validation="required" name="provider">
                                    <option value="0">--Choose--</option>
                                @foreach ($creditors as $item)
                                        <option {{isset($bank)&&$bank->provider_id==$item->id?"selected":'  '}} value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                                </select>

                            </div>
                        </div>




                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/bank-accounts') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
