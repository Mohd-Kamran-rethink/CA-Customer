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
            <div class="">

                <div class="float-right">Add Client:<span class="font-weight-bold float-right">Alt+C</span> </div> <br>
                <div class="float-right">Close Pop Up: <span class="font-weight-bold float-right">Esc</span></div>
            </div>

        </div>

    </section>

    <section class="content-header">

        <div id="transDetails">

        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-body">
                <form action="{{ url('transactions/change-status') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="hiddenid" value="{{ isset($transaction) ? $transaction->id : '' }}">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-8 col-md-6 col-lg-8">
                                    <div class="">
                                        <label>Clients <span style="color:red">*</span></label>
                                        <select id="client-ajax-dropdown" tabindex="1" name="client" id=""
                                            class="form-control searchOptions" onchange="handleClientChange(this)">
                                            <option value="0">--Choose--</option>
                                            @foreach ($clients as $item)
                                                <option value="{{ $item->id }}"
                                                    data-exchange-id="{{ $item->exchange_id }}">
                                                    {{ $item->number }} - {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('client')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-4 mt-4">
                                    <div class=" mt-2 ml-3">
                                        <label style="visibility: hidden">Client <span style="color:red">*</span></label>
                                        <button id="create-client-button" onclick="openClientModel()" type="button"
                                            class="btn btn-primary">Add
                                            Client</button>
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <div class="form-group">
                                        <label>Exchange<span style="color:red">*</span></label>
                                        <select name="exchange_id" class="form-control">
                                            <option value="">--Choose--</option>
                                            @foreach ($exchanges as $item)
                                                <option
                                                    {{ isset($transaction) && $transaction->exchange_id == $item->id ? 'selected' : (old('excahnge_id') == $item->id ? 'selected' : '') }}
                                                    value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('exchange_id')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Amount <span style="color:red">*</span></label>
                                        <input tabindex="2" readonly oninput="sumAmountBonus()" type="number"
                                            name="amount"
                                            value="{{ isset($transaction) ? $transaction->amount : old('amount') }}"
                                            id="amount" class="form-control" data-validation="required">
                                        @error('amount')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Bonus %</label>
                                        <input tabindex="3" oninput="calculateBonuspercent(this.value)" step="any"
                                            id="bonus_percent" class="form-control" data-validation="required">
                                        @error('bonus_percent')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Bonus</label>
                                        <input tabindex="4" oninput="sumAmountBonus()" type="number" step="any"
                                            name="bonus"
                                            value="{{ isset($transaction) ? $transaction->bonus : old('bonus') }}"
                                            id="bonus" class="form-control">
                                        @error('bonus')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Total<span style="color:red">*</span></label>
                                        <input type="number" name="total"
                                            value="{{ isset($transaction) ? $transaction->amount : old('total') }}"
                                            id="total" readonly class="form-control" data-validation="required">
                                        @error('total')
                                            <span class="text-danger">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-6">
                            <div class="col-12 ">
                                <div class="form-group">
                                    <label>Date <span style="color:red">*</span></label>
                                    <input readonly type="date" name="date"
                                        value="{{ isset($transaction) ? $transaction->date : $todaysdate }}"
                                        id="date" placeholder="100" class="form-control"
                                        data-validation="required">
                                    @error('date')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>



                            <div class="col-12" style="display: none">
                                <div class="form-group">
                                    <label>Created On <span style="color:red">*</span></label>
                                    <input readonly type="text" name="created_on"
                                        value="{{ isset($transaction) ? $transaction->created_at : $currentDateTime }}"
                                        id="phone" placeholder="100" class="form-control"
                                        data-validation="required">
                                    @error('created_on')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Bank Account<span style="color:red">*</span></label>
                                    <select disabled readonly name="bank_account" class="form-control">
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
                            <div class="col-12">
                                <div class="form-group">
                                    <label>UTR No <span style="color:red">*</span></label>
                                    <input readonly type="text" name="utr"
                                        value="{{ isset($transaction) ? $transaction->utr_no : old('utr') }}"
                                        id="utr" placeholder="UTR Number" class="form-control"
                                        data-validation="required">
                                    @error('utr')
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
                            <button tabindex="5" type="submit" class="btn btn-info">Accept</button>
                            <button onclick="openCancelModal({{ $transaction->id }})" type="button"
                                class="btn btn-default">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    {{-- client add pop up --}}
    <div class="modal fade show" id="client-modal" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Add Client</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="m-2" action="{{ url('/clients/add') }}" method="POST"id="client-add-form-popup">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="userId" value="{{ isset($client) ? $client->id : '' }}">
                        <h5 style="color:red;display: none" class="px-2" id="client-error-note">Please fill all input
                        </h5>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Exchange<span style="color:red">*</span></label>
                                <select name="exchange" class="form-control" id="exchange">
                                    <option value="0">--Choose--</option>
                                    @foreach ($exchanges as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('exchange')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Phone <span style="color:red">* Don't use 91 in phone number</span></label>
                                <input {{ isset($client) ? 'readonly' : '' }} name="number" maxlength="10"
                                    value="{{ isset($client) ? $client->number : old('number') }}" id="number"
                                    type="text" class="form-control" onpaste="removePrefix(event)">
                                <span id="client_name_error" class="text-danger">
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>ID Name <span style="color:red">*</span></label>
                                <input type="text" id="client_ca_id" name="ca_id"
                                    {{ isset($client) ? 'readonly' : '' }}
                                    value="{{ isset($client) ? $client->ca_id : old('ca_id') }}" id="ca_id"
                                    placeholder="ID Name" class="form-control">
                                @error('ca_id')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Client Name </label>
                                <input type="text" id="client_name" name="name" placeholder="John"
                                    class="form-control" data-validation="required"
                                    value="{{ isset($client) ? $client->name : old('name') }}">
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>


                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button onclick="submitClientAdd()" type="submit" id="submit-button"
                                class="btn btn-info">Save</button>
                            <button type="button" class="btn btn-primary " data-dismiss="modal" aria-label="Close">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                        <span style="color:red;display: none" id="cancel_note_error">Please write cancel note!</span>
                    </div>
                    <div class="modal-footer ">
                        <button id="submit-button" onclick="submitStatusChange()" type="submit"
                            class="btn btn-danger">Cancel</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Close</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openClientModel() {
            $('#client-modal').modal('show');
        }

        function submitStatusChange() {

            let submitButton = $('#submit-button')
            let cancel_note = $('#cancel_note')
            event.preventDefault();
            if (cancel_note.val().length == 0) {
                console.log("first")
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

        function calculateBonuspercent(percent) {
            let amountinput = $('#amount');
            let bonusinput = $('#bonus');
            let total = $('#total');
            bonusinput.val((amountinput.val() * percent) / 100)
            total.val(parseFloat(amountinput.val()) + parseFloat((amountinput.val() * percent) / 100));
        }
    </script>
    {{-- client add ajax --}}
    <script>
        function submitClientAdd() {
            event.preventDefault();
            let submitButton = $('#submit-button')
            let client_name = $('#client_name').val();
            let client_number = $('#number').val();
            let client_ca_id = $('#client_ca_id').val();
            let exchange = $('#exchange').val();
            var dropdown = document.getElementById('client-ajax-dropdown');


            $.ajax({
                url: BASE_URL + "/clients/add?name=" + client_name + "&number=" + client_number + "&ca_id=" +
                    client_ca_id + "&exchange=" + exchange,
                success: function(response) {
                    if (response.client && response.data) {
                        $('#client-modal').modal('hide');
                        $("#client-ajax-dropdown").html(response.data);
                        handleClientChange(dropdown);
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
    </script>
    {{-- on clienet select select exchnage auto --}}
    <script>
        function handleClientChange(selectElement) {

            let selectedOption = selectElement.options[selectElement.selectedIndex];
            console.log(selectedOption)
            let exchangeId = selectedOption.getAttribute('data-exchange-id');
            let exchangeSelect = document.querySelector('select[name="exchange_id"]');

            giveclientHistory(selectedOption.value)
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

        function giveclientHistory(id)

        {
            $.ajax({
                url: BASE_URL +
                    "/getClientHistory/?clientID=" + id,
                success: function(data) {
                    $("#transDetails").html(data);

                },
            });
        }
    </script>
    <script>
        document.addEventListener('keydown', function(event) {
            // Check if the key pressed is the desired shortcut (e.g., "Ctrl + Alt + C")
            if (event.altKey && event.key === 'c') {
                // Call the openClientModel function

                openClientModel();
            }
            if (event.key === 'Escape') {
                console.log("first")
                $('#client-modal').modal('hide');
            }
        });
    </script>
    {{-- remove phone input prefix --}}
    <script>
        function removePrefix(event) {
            var clipboardData = event.clipboardData || window.clipboardData;
            var pastedData = clipboardData.getData('text');
            var phoneNumber = pastedData.replace('+91', '');
            event.preventDefault();
            $('#number').val(phoneNumber);
        }
    </script>
@endsection
