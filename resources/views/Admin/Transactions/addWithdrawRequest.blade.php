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
                        <div class="col-6">
                            <input type="hidden" name="hiddenid" value="{{ isset($transaction) ? $transaction->id : '' }}">
                            <div class="col-12 mb-2">
                                <label>Clients <span style="color:red">*</span></label>
                                <select tabindex="1" {{ !isset($transaction) ? '' : 'disabled' }}
                                    onchange="renderClients(this)" name="client" id="selected-client"
                                    class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($clients as $item)
                                        <option
                                            {{ isset($transaction) && $transaction->client_id == $item->id ? 'selected' : '' }}
                                            value="{{ $item->id }}" data-number="{{ $item->number }}"
                                            data-client="{{ $item->id }}" data-exchange-id="{{ $item->exchange_id }}"> {{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('client')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                                {{-- <div class=" ml-3">
                                 <label style="visibility: hidden"> Client <span style="color:red">*</span></label>
                                 <button type="button" onclick="openClientModel()" class="btn btn-primary">Add
                                 Client</button>
                              </div> --}}
                            </div>

                            <div class="col-12 d-flex  mb-2">
                                <div style="width: -webkit-fill-available;" id="bank-account-append">
                                    <label>Client Bank Account<span style="color:red">*</span></label>
                                    <select tabindex="2" name="client_bank_account" class="form-control">
                                        <option value="">--Choose--</option>
                                        @if (isset($transaction))
                                            @foreach ($banks as $item)
                                                <option {{ $item->id == $transaction->customer_bank_id ? 'selected' : '' }}
                                                    value='{{ $item->id }}'>{{ $item->holder_name }} -(
                                                    {{ $item->account_number }} )</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('client_bank_account')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                                <div class=" ml-4">
                                    <label style="visibility: hidden"> bank <span style="color:red">*</span></label>
                                    <button type="button" onclick="openBankModal()" class="btn btn-primary">Add
                                        Account</button>
                                </div>
                            </div>
                            <div class="col-12">
                                <div style="width: -webkit-fill-available;" id="exchange_id">
                                    <label>Exchange<span style="color:red">*</span></label>
                                    <select name="exchange_id" class="form-control">
                                        <option value="0">--Choose--</option>
                                        @foreach ($exchanges as $item)
                                            <option
                                                {{ isset($transaction) && $item->id == $transaction->exchange_id ? 'selected' : '' }}
                                                value='{{ $item->id }}'>{{ $item->name }} </option>
                                        @endforeach
                                    </select>
                                    @error('exchange_id')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <input readonly type="hidden" name="created_on"
                                value="{{ isset($transaction) ? $transaction->created_at : $currentDateTime }}"
                                class="form-control" data-validation="required">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Amount <span style="color:red">*</span></label>
                                    <input tabindex="3" oninput="sumAmountBonus()" type="number" name="amount"
                                        value="{{ isset($transaction) ? $transaction->amount : old('amount') }}"
                                        id="amount" class="form-control" data-validation="required">
                                    @error('amount')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <label>Bonus</label>
                                <input tabindex="4" type="number" step="any"
                                    name="bonus" value="{{ isset($transaction) ? $transaction->bonus : old('bonus') }}"
                                    id="bonus" class="form-control">
                                @error('bonus')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Total<span style="color:red">*</span></label>
                                    <input type="number" name="total"
                                        value="{{ old('total') }}"
                                        id="total" readonly class="form-control" data-validation="required">
                                    @error('total')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
   
                        </div>
                        <div class="col-6">
                           <div class="col-12">
                               <div class="form-group">
                                   <label>Date <span style="color:red">*</span></label>
                                   <input type="date" name="date"
                                       value="{{ isset($transaction) ? $transaction->date : $todaysdate }}" id="date"
                                       class="form-control" data-validation="required">
                                   @error('date')
                                       <span class="text-danger">
                                           {{ $message }}
                                       </span>
                                   @enderror
                               </div>
                           </div>
                       </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <button tabindex="4" type="submit" class="btn btn-info">Save</button>
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
                        <input type="hidden" id="hidden_client_id" name="customer_id">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Account Holder Name<span style="color:red">*</span></label>
                                <input id="name" type="text" name="name" class="form-control"
                                    value="{{ isset($bank) ? $bank->holder_name : old('name') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Bank Name<span style="color:red">*</span></label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control"
                                    value="{{ isset($bank) ? $bank->bank_name : old('bank_name') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Account Number <span style="color:red">*</span></label>
                                <input type="number" name="account_number"
                                    value="{{ isset($bank) ? $bank->account_number : old('accout_number') }}"
                                    id="account_number" class="form-control" data-validation="required">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>IFSC Code <span style="color:red">*</span></label>
                                <input type="text" id="ifcs_code" name="ifcs_code"
                                    value="{{ isset($bank) ? $bank->ifsc : old('ifcs_code') }}" id="ifcs_code"
                                    class="form-control" data-validation="required">
                            </div>
                        </div>
                        {{-- 
               <div class="col-12">
                  <div class="form-group">
                     <label>Phone <span style="color:red">*</span></label>
                     <input type="number" name="phone"
                        value="{{ isset($bank) ? $bank->phone : old('phone') }}" id="phone"
                        class="form-control" data-validation="required">
                  </div>
               </div>
               --}}
                        <div class="col-12">
                            <div class="form-group">
                                <label>Email </label>
                                <input type="email" name="email"
                                    value="{{ isset($bank) ? $bank->email : old('email') }}" id="email"
                                    class="form-control" data-validation="required">
                            </div>
                        </div>
                        {{-- 
               <div class="col-12">
                  <div class="form-group">
                     <label>Address <span style="color:red">*</span></label>
                     <textarea name="address" class="form-control" id="address" cols="20" rows="4">{{ isset($bank) ? $bank->address : old('address') }}</textarea>
                  </div>
               </div>
               --}}
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button onclick="addBankAccoutAjax()" type="button" class="btn btn-info">Save & Next</button>
                            <a href="{{ url('/banks') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function renderClients(selectedClient) {
            let client_id = jQuery('#selected-client').val();
            jQuery.ajax({
                url: BASE_URL + "/render-client-account/?client_id=" + client_id,
                success: function(data) {
                    if (data) {
                        jQuery("#bank-account-append").html(data);
                    }
                }
            });
            // select exhcnag value
            handleClientChange(selectedClient)
        }

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
            // let selectedNumber = selectedOption.data("number");
            let client_id = selectedOption.data("client");
            // $('#phone').val(selectedNumber);
            $('#hidden_client_id').val(client_id);
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
            let customer_id = $('#hidden_client_id').val()

            $.ajax({
                url: BASE_URL +
                    "/bankaccount/add?name=" + name + '&account_number=' + account_number +
                    '&address=' + address + '&bank_name=' + bank_name + '&email=' +
                    email + '&ifcs_code=' + ifcs_code + '&phone=' + phone + '&customer_id=' +
                    customer_id,
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

        function handleClientChange(selectElement) {
            let selectedOption = selectElement.options[selectElement.selectedIndex];
            let exchangeId = selectedOption.getAttribute('data-exchange-id');
            let exchangeSelect = document.querySelector('select[name="exchange_id"]');

            // Loop through each option in the exchange select dropdown
            for (let i = 0; i < exchangeSelect.options.length; i++) {
                let option = exchangeSelect.options[i];

                if (option.value === exchangeId) {
                    // Set the selected attribute for the option with the given exchange ID
                    option.selected = true;
                    break;
                }
            }

        }
    </script>
@endsection
