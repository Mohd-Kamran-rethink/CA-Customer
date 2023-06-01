@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($transaction) ? 'Edit Transaction' : 'Add Transaction' }}</h1>
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
                <form action="{{ isset($transaction) ? url('transactions/edit') : url('transactions/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{ isset($transaction) ? $transaction->id : '' }}">

                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label>Date <span style="color:red">*</span></label>
                                <input type="date" name="date"
                                    value="{{ isset($transaction) ? $transaction->date : $todaysdate }}" id="date"
                                    placeholder="100" class="form-control" data-validation="required">
                                @error('date')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label>Amount <span style="color:red">*</span></label>
                                <input oninput="sumAmountBonus()" type="number" name="amount"
                                    value="{{ isset($transaction) ? $transaction->amount : old('amount') }}" id="amount"
                                    class="form-control" data-validation="required">
                                @error('amount')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <input readonly type="hidden" name="created_on"
                            value="{{ isset($transaction) ? $transaction->created_at : $currentDateTime }}" id="phone"
                            class="form-control" data-validation="required">
                        <input oninput="sumAmountBonus()" type="hidden" name="bonus"
                            value="{{ isset($transaction) ? $transaction->bonus : old('bonus') }}" id="bonus"
                            class="form-control">

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label>UTR No <span style="color:red">*</span></label>
                            <input type="text" name="utr"
                                value="{{ isset($transaction) ? $transaction->utr_no : old('utr') }}" id="utr"
                                placeholder="UTR Number" class="form-control" data-validation="required">
                            @error('utr')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>


                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label>Bank Account<span style="color:red">*</span></label>
                            <select name="bank_account" class="form-control">
                                <option value="">--Choose--</option>
                                @foreach ($banks as $item)
                                    <option
                                        {{ isset($transaction) && $transaction->bank_account == $item->id ? 'selected' : (old('bank_account') == $item->id ? 'selected' : '') }}
                                        value="{{ $item->id }}">{{ $item->holder_name }}</option>
                                @endforeach
                            </select>

                            @error('bank_account')
                                <span class="text-danger">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>
                    </div>
                    <input type="hidden" name="total"
                        value="{{ isset($transaction) ? $transaction->total : old('total') }}" id="total" readonly
                        class="form-control" data-validation="required">


            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <button type="submit" class="btn btn-info">Save</button>
                    <a href="{{ url('/dashboard') }}" type="button" class="btn btn-default">Cancel</a>
                </div>
            </div>
            </form>
        </div>
        </div>
    </section>

    <script>
        function sumAmountBonus() {
            let amount = parseFloat($('#amount').val());
            let bonus = parseFloat($('#bonus').val());
            let total = $('#total');
            total.val((amount || 0) + (bonus || 0));
        }
    </script>
@endsection
