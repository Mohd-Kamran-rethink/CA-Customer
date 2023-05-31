@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($transaction) ? 'Edit Withdraw Request' : 'Add Withdraw Request' }}</h1>
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
                <form
                    action="{{ isset($transaction) ? url('transactions/withdraw/edit') : url('transactions/withdraw/add') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{ isset($transaction) ? $transaction->id : '' }}">
                        <div class="col-6 d-flex mb-2">
                            <div class=" " style="width: -webkit-fill-available;" id="client-ajax-dropdown">
                                <label>Clients <span style="color:red">*</span></label>
                                <select name="client" id="selected-client" class="form-control">
                                    <option value="0">--Choose--</option>
                                    @foreach ($clients as $item)
                                        <option value="{{ $item->id }}" data-number="{{ $item->number }}">
                                            {{ $item->name }}</option>
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
                                <button type="button" onclick="openClientModel()" class="btn btn-primary">Add
                                    Client</button>
                            </div>
                        </div>
                        <div class="col-6 d-flex  mb-2">
                            <div style="width: -webkit-fill-available;" id="bank-account-append">
                                <label>Bank Account<span style="color:red">*</span></label>
                                <select name="bank_account" class="form-control">
                                    <option value="">--Choose--</option>
                                    @foreach ($banks as $item)
                                        <option
                                            {{ isset($transaction) && $transaction->bank_account == $item->id ? 'selected' : (old('bank_account') == $item->id ? 'selected' : '') }}
                                            value="{{ $item->id }}">{{ $item->holder_name }}- ({{$item->account_number}})</option>
                                    @endforeach
                                </select>
                                @error('bank_account')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class=" ml-3">
                                <label style="visibility: hidden"> bank <span style="color:red">*</span></label>
                                <button type="button" onclick="openBankModal()" class="btn btn-primary">Add
                                    Account</button>
                            </div>

                        </div>
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
                                    placeholder="100" class="form-control" data-validation="required">
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
                                    placeholder="100" class="form-control" data-validation="required">
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
                                <input oninput="sumAmountBonus()" type="number" name="bonus"
                                    value="{{ isset($transaction) ? $transaction->bonus : old('bonus') }}" id="bonus"
                                    placeholder="100" class="form-control">
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
                                    id="utr" placeholder="UTR Number" class="form-control"
                                    data-validation="required">
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
    <div class="modal fade show" id="bank-modal" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add Bank Account</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="mx-3 my-3"
                    action="{{ isset($bank) ? url('bank-accounts/edit') : url('bank-accounts/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Account Holder Name<span style="color:red">*</span></label>
                                <input id="name" type="text" name="name" placeholder="Account Holder Name"
                                    class="form-control" value="{{ isset($bank) ? $bank->holder_name : old('name') }}">

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Bank Name<span style="color:red">*</span></label>
                                <input type="text" name="bank_name" id="bank_name" placeholder="ABC Bank"
                                    class="form-control"
                                    value="{{ isset($bank) ? $bank->bank_name : old('bank_name') }}">

                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Account Number <span style="color:red">*</span></label>
                                <input type="number" name="account_number"
                                    value="{{ isset($bank) ? $bank->account_number : old('accout_number') }}"
                                    id="account_number" placeholder="100" class="form-control"
                                    data-validation="required">


                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>IFSC Code <span style="color:red">*</span></label>
                                <input type="number" id="ifcs_code" name="ifcs_code"
                                    value="{{ isset($bank) ? $bank->ifsc : old('ifcs_code') }}" id="ifcs_code"
                                    placeholder="100" class="form-control" data-validation="required">


                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Phone <span style="color:red">*</span></label>
                                <input type="number" name="phone"
                                    value="{{ isset($bank) ? $bank->phone : old('phone') }}" id="phone"
                                    placeholder="+91 0128882223" class="form-control" data-validation="required">


                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Email </label>
                                <input type="email" name="email"
                                    value="{{ isset($bank) ? $bank->email : old('email') }}" id="email"
                                    placeholder="+91 0128882223" class="form-control" data-validation="required">


                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Address </label>
                                <textarea name="address" class="form-control" id="address" cols="20" rows="4" placeholder="Address">{{ isset($bank) ? $bank->address : old('address') }}</textarea>

                            </div>
                        </div>



                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button onclick="addBankAccoutAjax()" type="button" class="btn btn-info">Save</button>
                            <a href="{{ url('/banks') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openClientModel() {
            $('#client-modal').modal('show');
        }

        function openBankModal() {
            let name = $('#name').val('')
            let account_number = $('#account_number').val('')
            let address = $('#address').val('')
            let bank_name = $('#bank_name').val('')
            let email = $('#email').val('')
            let ifcs_code = $('#ifcs_code').val('')
            let phone = $('#phone').val('')
            let selectedOption = $("#selected-client option:selected");
            let selectedNumber = selectedOption.data("number");
            $('#phone').val(selectedNumber);
            $('#bank-modal').modal('show');
        }

        function addBankAccoutAjax() {
            let name = $('#name').val()
            let account_number = $('#account_number').val()
            let address = $('#address').val()
            let bank_name = $('#bank_name').val()
            let email = $('#email').val()
            let ifcs_code = $('#ifcs_code').val()
            let phone = $('#phone').val()

            $.ajax({
                url: BASE_URL + "/bankaccount/add?name=" + name + '&account_number=' + account_number +
                    '&address=' + address + '&bank_name=' + bank_name + '&email=' +
                    email + '&ifcs_code=' + ifcs_code+ '&phone=' + phone,
                success: function(data) {
                    if (data) {
                        $('#bank-modal').modal('hide');
                        $("#bank-account-append").html(data);
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.errors) {

                        // Loop through each error and display it on the respective input field
                        $.each(response.errors, function(key, value) {
                            console.log(key)
                            var inputElement = $('#' + key);
                            inputElement.addClass('is-invalid');
                            inputElement.next('.invalid-feedback').html(value[0]);
                        });
                    } else {
                        // Handle other error cases
                    }
                }
            });

        }

        function sumAmountBonus() {
            let amount = parseFloat($('#amount').val());
            let bonus = parseFloat($('#bonus').val());
            let total = $('#total');
            total.val((amount || 0) + (bonus || 0));
        }
    </script>
@endsection
