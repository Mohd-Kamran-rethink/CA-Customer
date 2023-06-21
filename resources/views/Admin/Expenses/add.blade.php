@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($expenses) ? 'Edit Payment Types' : 'Add Payment Types' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-body">
                <form id="expense-form" action="{{ isset($expenses) ? url('expenses/edit') : url('expenses/add') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="expensesId" value="{{ isset($expenses) ? $expenses->id : '' }}">
                        <div class="col-4">
                            <div class="form-group">
                                <label>Type<span style="color:red">*</span></label>
                                <select onchange="mainTypeChange(this.value)" name="main_type" id="main_type"
                                    class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    <option value="Transfer">Transfer</option>
                                    <option value="Expense">Expense</option>
                                </select>
                                @error('main_type')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 transfer" style="display: none" id="for-transfer">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Transfer Type<span style="color:red">*</span></label>
                                <select onchange="transferType(this.value)" name="transfer_type" id="transfer_type"
                                    class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    <option value="Internal">Internal Transfer</option>
                                    <option value="Third Party">Third Party</option>
                                </select>
                                @error('main_type')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 transferType-third" style="display: none">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Transaction Type<span style="color:red">*</span></label>
                                <select onchange="handleBankInput(this.value)" name="transaction_type" id="transaction_type"
                                    class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Cash">Cash</option>
                                </select>
                                @error('transaction_type')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 transferType-third" style="display: none">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Accounting Type<span style="color:red">*</span></label>
                                <select name="accounting_type" id="accounting_type" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Credit">Credit</option>
                                </select>
                                @error('accounting_type')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 transferType-third" id="for-transfer" style="display: none">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Reciever Name<span style="color:red">*</span></label>
                                <select name="creditor_id" id="creditor_id" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($users as $item)
                                        @if ($item->is_admin == 'Yes')
                                            @continue
                                        @endif
                                        <option value="{{ $item->id }}">{{ $item->name }} - ( {{ $item->phone }} )
                                        </option>
                                    @endforeach
                                </select>
                                @error('creditor_id')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 transfer" id="sender-bank" style="display: none">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Sender Bank<span style="color:red">*</span></label>
                                <select name="sender_bank" id="sender_bank" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($banks as $item)
                                        <option value="{{ $item->id }}">{{ $item->account_number }} - (
                                            {{ $item->holder_name }} )
                                        </option>
                                    @endforeach
                                </select>
                                @error('sender_bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 transferType-internal" style="display: none">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Reciever Bank<span style="color:red">*</span></label>
                                <select name="receiver_bank" id="receiver_bank" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($banks as $item)
                                        <option value="{{ $item->id }}">{{ $item->account_number }} - (
                                            {{ $item->holder_name }} )
                                        </option>
                                    @endforeach
                                </select>
                                @error('receiver_bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{-- expense --}}
                    <div id="expense-forms" class="row" style="display: none">
                        <div class="col-4">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Department<span style="color:red">*</span></label>
                                <select onchange="renderExpenseType(this.value)" name="department_id" id="department_id"
                                    class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($departments as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" id="error_department_id" style="display: none">
                                    This field is required
                                </span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group" id="expense-type-render"
                                style="display: flex;flex-direction: column">
                                <label>Expense Type<span style="color:red">*</span></label>
                                <select disabled name="expensse_type" id="expense-type"
                                    class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($expenseTypes as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('expense-type')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Transaction Type<span style="color:red">*</span></label>
                                <select onchange="handleTransType(this.value)" name="transactionType"
                                    id="transactionType" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                                @error('transactionType')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4" id="from_bank_trans">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Bank<span style="color:red">*</span></label>
                                <select name="from_bank" id="from_bank" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($banks as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->account_number . ' - (' . $item->holder_name . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Attatchement</label>
                                <input type="file" name="attatchement" class="form-control"
                                    data-validation="required"
                                    value="{{ isset($expenses) ? $expenses->amount : old('amount') }}">
                                @error('attatchement')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        {{-- <div class="col-4">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Currency<span style="color:red">*</span></label>
                                <select name="currency" id="currency" class="form-control">
                                    <option value="0">--Choose--</option>
                                    <option value="rupee">Rupee</option>
                                    <option value="aed">AED</option>
                                    <option value="usd">USD</option>
                                </select>
                                @error('currency')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="col-4" style="display: flex;flex-direction: column">
                            <div class="form-group">
                                <label>Amount<span style="color:red">*</span></label>
                                <input type="number" name="amount" class="form-control" data-validation="required"
                                    value="{{ isset($expenses) ? $expenses->amount : old('amount') }}">
                                @error('amount')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="col-4" style="display: flex;flex-direction: column">
                            <div class="form-group">
                                <label>Currency Value<span style="color:red">*</span></label>
                                <input step="any" type="number" name="currency_rate" class="form-control"
                                    inputmode="decimal"
                                    value="{{ isset($expenses) ? $expenses->amount : old('amount') }}">
                                @error('currency_rate')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="col-4">
                            <div class="form-group" style="display: flex;flex-direction: column">
                                <label>Remark</label>
                                <textarea name="remark" id="" cols="1" rows="" class="form-control"></textarea>
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
                            <button onclick="submitForm()" type="submit" id="submit"
                                class="btn btn-info">Save</button>
                            <a href="{{ url('/expense-type') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        function renderExpenseType(id) {
            let expenseTypeInput = $('#expense-type')
            if (id == 0) {
                expenseTypeInput.attr('disabled', true);
            } else {
                $.ajax({
                    url: BASE_URL +
                        "/expenses/render-expense-type?department=" + id,
                    success: function(data) {
                        expenseTypeInput.removeAttr('disabled');
                        $("#expense-type-render").html(data);

                    },
                });
            }
        }

        function mainTypeChange(value) {
            if (value == 'Expense') {
                $('#expense-forms').css('display', 'flex');
                $('.transferType-third').hide()
                $('.transferType-internal').hide()
                $('.transfer').hide()
            } else if (value == 'Transfer') {
                $('#expense-forms').css('display', 'none');
                $('#for-transfer').show();
            } else {
                $('#expense-forms').css('display', 'none');
                $('.transferType-third').hide()
                $('.transferType-internal').hide()
                $('.transfer').hide()
            }
        }

        function handleBankInput(value) {
            if (value === "Bank") {
                $('#sender-bank').show();
            } else {
                $('#sender-bank').hide();
            }

        }

        function transferType(value) {
            if (value == "Third Party") {

                $('#sender-bank').hide();
                $('.transferType-third').show()
                $('.transferType-internal').hide()

            } else if (value == "Internal") {
                $('#sender-bank').show();
                $('.transferType-third').hide()
                $('.transferType-internal').show()

            }
        }

        function handleTransType(value) {
            console.log(value)
            if (value == 'Bank') {
                $('#from_bank_trans').show()
            } else {
                $('#from_bank_trans').hide()

            }
        }
    </script>
@endsection
