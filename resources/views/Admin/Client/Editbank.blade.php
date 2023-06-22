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
                <form action="{{ isset($bank) ? url('client/bank-accounts/edit') : url('bank-accounts/add') }}" method="POST"
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
                       




                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/banks') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
