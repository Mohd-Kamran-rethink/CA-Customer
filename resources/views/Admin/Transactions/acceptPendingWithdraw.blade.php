@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($transaction) ? 'Withdraw Status Change' : '' }}</h1>
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
                <form action="{{ isset($transaction) ? url('transactions/change-status-withdraw') : '' }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{ isset($transaction) ? $transaction->id : '' }}">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group" id="client-ajax-dropdown">
                                <label>Clients <span style="color:red">*</span></label>
                                <select readonly name="client"  class="form-control">
                                    <option value="0">--Choose--</option>
                                    @foreach ($clients as $item)
                                        <option
                                            {{ isset($transaction) && $transaction->client_id == $item->id ? 'selected' : (old('client') == $item->id ? 'selected' : '') }}
                                            value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
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
                                <label>UTR No <span style="color:red">
                                        {{ session('user')->role === 'withdrawrer' ? '' : '*' }}</span></label>
                                <input {{ session('user')->role === 'withdrawrer' ? 'readonly' : '' }} type="number"
                                    name="utr" value="{{ isset($transaction) ? $transaction->utr_no : old('utr') }}"
                                    id="utr" placeholder="UTR Number" class="form-control" data-validation="required">
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
                    </div>
                            {{-- customer bank deatils --}}
                            {{-- <h5 class="font-weight-bold">Customer Bank Details</h5> --}}
                            {{-- <span class="font-weight-bold">Account Holder Name: </span>{{$transaction->holder_name}} <br>
                            <span class="font-weight-bold">Bank Name: </span>{{$transaction->customer_bank_name}} <br>
                            <span class="font-weight-bold">Account Number: </span>{{$transaction->customer_account_number}} <br>
                            <span class="font-weight-bold">IFSC Code: </span>{{$transaction->customer_ifsc}} <br>
                            <span class="font-weight-bold">Phone Number: </span>{{$transaction->customer_phone}} <br> --}}
                            <table class="table mt-4 mb-4">
                                <thead>
                                  <tr>
                                    <th scope="col">Customer Bank Details</th>
                                    {{-- <th scope="col">Value</th> --}}
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td>Account Holder Name</td>
                                    <td>{{$transaction->holder_name}}</td>
                                  </tr>
                                  <tr>
                                    <td>Bank Name</td>
                                    <td>{{$transaction->customer_bank_name}}</td>
                                  </tr>
                                  <tr>
                                    <td>Account Number</td>
                                    <td>{{$transaction->customer_account_number}}</td>
                                  </tr>
                                  <tr>
                                    <td>IFSC Code</td>
                                    <td>{{$transaction->customer_ifsc}}</td>
                                  </tr>
                                  <tr>
                                    <td>Phone Number</td>
                                    <td>9876543210</td>
                                  </tr>
                                  <!-- Add more rows for additional bank details -->
                                </tbody>
                              </table>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Accept</button>
                            <button onclick="openCancelModal({{ $transaction->id }})" type="button"
                                class="btn btn-default">Cancel</button>
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
                        <label for="">Cancel Note <span style="color:red">*</span></label>
                        <textarea name="cancel_note" id="cancel_note" cols="30" rows="3" class="form-control"
                            placeholder="Write something"></textarea>
                        <span style="color:red;display: none" id="cancel_note_error">Pleace write cancel note!</span>
                    </div>
                    <div class="modal-footer ">
                        <button onclick="submitStatusChange()" type="submit" id="submit-button"
                            class="btn btn-danger">Cancel</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Close</button>
                </form>
            </div>
        </div>
    </div>
   
    <script>
        function submitStatusChange() {
            let submitButton = $('#submit-button')
            let cancel_note = $('#cancel_note')
            event.preventDefault();
            if (cancel_note.val().length == 0) {
                $('#cancel_note_error').show()
            } else {
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
