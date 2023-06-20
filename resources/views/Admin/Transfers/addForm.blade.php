@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($transfer) ? 'Edit Transfer' : 'Add Transfer' }}</h1>
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
                <form action="{{ isset($transfer) ? url('transfers/edit') : url('transfers/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{ isset($transfers) ? $transfers->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>From Bank<span style="color:red">*</span></label>
                                <select tabindex="1" name="from_bank" class="form-control">
                                    <option value="0">--Choose--</option>
                                    @foreach ($banks as $item)
                                        <option value="{{$item->id}}">{{ $item->holder_name }} - {{ $item->bank_name }} -
                                            {{ $item->account_number }}</option>
                                    @endforeach
                                </select>

                                @error('from_bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>To Bank <span style="color:red">*</span></label>
                                <select tabindex="2" name="to_bank" class="form-control">
                                    <option value="0">--Choose--</option>
                                    @foreach ($banks as $item)
                                    <option value="{{$item->id}}">{{ $item->holder_name }} - {{ $item->bank_name }} -
                                        {{ $item->account_number }}</option>
                                @endforeach
                                </select>
                                @error('to_bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Amount<span style="color:red">*</span></label>
                                <input tabindex="3" type="number" step="any" name="amount"
                                    value="{{ isset($transfer) ? $transfer->amount : old('amount') }}" class="form-control">
                                @error('amount')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        {{-- remark --}}
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Remark</label>
                                <input tabindex="4" type="text" name="remark"
                                    value="{{ isset($transfer) ? $transfer->remark : old('remark') }}" class="form-control">
                                @error('remark')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button tabindex="5" type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/transfers') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
