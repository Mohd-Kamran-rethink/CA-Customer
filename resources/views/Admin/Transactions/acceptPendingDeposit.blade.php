@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($transaction) ? 'Transaction Status Change' : 'Add Transaction' }}</h1>
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
                <form action="{{ url('transactions/change-status') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{ isset($transaction) ? $transaction->id : '' }}">
                        <div class="col-6 d-flex">
                            <div class=" " style="width: -webkit-fill-available;" id="client-ajax-dropdown">
                                <label>Clients <span style="color:red">*</span></label>
                                <select name="client" id="" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($clients as $item)
                                        <option value="{{$item->id}}">{{$item->number}}({{$item->name}})</option>
                                    @endforeach
                                </select>
                                @error('client')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class=" ml-3">
                                <label style="visibility: hidden"> Client <span style="color:red">*</span></label>
                                <button onclick="openClientModel()" type="button" class="btn btn-primary">Add Client</button>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label>Date <span style="color:red">*</span></label>
                                <input readonly type="date" name="date"
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
                                <input readonly oninput="sumAmountBonus()" type="number" name="amount"
                                    value="{{ isset($transaction) ? $transaction->amount : old('amount') }}" id="amount"
                                     class="form-control" data-validation="required">
                                @error('amount')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label>Created On <span style="color:red">*</span></label>
                                <input readonly type="text" name="created_on"
                                    value="{{ isset($transaction) ? $transaction->created_at : $currentDateTime }}"
                                    id="phone" placeholder="100" class="form-control" data-validation="required">
                                @error('created_on')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label>Bonus </label>
                                <input readonly oninput="sumAmountBonus()" type="number" name="bonus"
                                    value="{{ isset($transaction) ? $transaction->bonus : old('bonus') }}" id="bonus"
                                     class="form-control">
                                @error('bonus')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label>UTR No <span style="color:red">*</span></label>
                                <input  readonly type="text" name="utr"
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
                                <label>Total<span style="color:red">*</span></label>
                                <input type="number" name="total"
                                    value="{{ isset($transaction) ? $transaction->total : old('total') }}" id="total"
                                    readonly class="form-control" data-validation="required">
                                @error('total')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label>Bank Account<span style="color:red">*</span></label>
                                <select readonly name="bank_account" class="form-control">
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


                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Accept</button>
                            <button onclick="openCancelModal({{$transaction->id}})" type="button" class="btn btn-default">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <div class="modal fade show" id="cancel-transaction" style=" padding-right: 17px;" aria-modal="true"
        role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Are you sure you want to cancel this transaction?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/transactions/change-status/cancel') }}" method="POST" id="statsus-change-form">
                    @csrf
                    <input type="hidden" name="hiddenId" id="hiddenId">
                    <div class="modal-body">
                        <label for="">Cancel Note </label>
                        <textarea name="cancel_note" id="" cols="30" rows="3" class="form-control"
                            placeholder="Write something"></textarea>
                    </div>
                    <div class="modal-footer ">
                        <button onclick="submitStatusChange()" type="submit" class="btn btn-danger">Cancel</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Close</button>
                </form>
            </div>
        </div>
    </div>
     
    <script>
        function openClientModel()
        {
            $('#client-modal').modal('show');
        }
        function submitStatusChange()
        {
            let submitButton = $('#submit-button')
            let cancel_note = $('#cancel_note')
            event.preventDefault();
            if(cancel_note.val().length==0)
            {
                $('#cancel_note_error').show()
            }
            else
            {
                $('#statsus-change-form').submit();
            }
        }
        function openCancelModal(id) {
            $('#cancel-transaction').modal('show');
            $('#hiddenId').val(id)
        }

        function sumAmountBonus() {
            let amount = parseFloat($('#amount').val());
            let bonus = parseFloat($('#bonus').val());
            let total = $('#total');
            total.val((amount || 0) + (bonus || 0));
        }
    </script>
@endsection
