@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($transfer) ? 'Edit Transfer' : 'Add Transfer' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
                <h3>Credit means goes out</h3>
                <h3>Debit  means come IN</h3>
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
                                <label>Transfer Type<span style="color:red">*</span></label>
                                <select onchange="transferType(this.value)" tabindex="1" name="transfer_type"
                                    class="form-control">
                                    <option value="0">--Choose--</option>
                                    <option value="internal">Internal</option>
                                    <option value="external">Third Party</option>
                                    <option value="journal">Journal</option>
                                </select>
                                @error('from_bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 external-types-inputs" style="display: none">
                            <div class="form-group">
                                <label>Accounting Type<span style="color:red">*</span></label>
                                <select tabindex="1" name="accounting_type" class="form-control">
                                    <option value="0">--Choose--</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Credit">Credit</option>
                                </select>
                                @error('from_bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 external-types-inputs" style="display: none">
                            <div class="form-group">
                                <label>Payment Type<span style="color:red">*</span></label>
                                <select onchange="payemntType(this.value)" tabindex="1" name="payment_type"
                                    class="form-control">
                                    <option value="0">--Choose--</option>
                                    <option value="bank">Bank</option>
                                    <option value="cash">Cash</option>
                                </select>
                                @error('from_bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-4 to-bank-input">
                            <div class="form-group">
                                <label>Bank<span style="color:red">*</span></label>
                                <select tabindex="1" name="sender_bank" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($banks as $item)
                                        <option value="{{ $item->id }}">{{ $item->holder_name }} -
                                            {{ $item->bank_name }} -
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
                        <div class="col-4 external-types-inputs" style="display: none;" >
                            <div class="form-group">
                                <label>Ledger<span style="color:red">*</span></label>
                                <select tabindex="1" name="ledger_id" class="form-control searchOptions" style="width: 100%">
                                    <option value="0">--Choose--</option>
                                    @foreach ($ledgers as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>

                                @error('ledger_id')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4 internal-types-inputs">
                            <div class="form-group">
                                <label>To Bank <span style="color:red">*</span></label>
                                <select tabindex="2" name="receiver_bank" class="form-control searchOptions">
                                    <option value="0">--Choose--</option>
                                    @foreach ($banks as $item)
                                        <option value="{{ $item->id }}">{{ $item->holder_name }} -
                                            {{ $item->bank_name }} -
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
                        <div class="col-4 journal" style="display: none;" >
                            <div class="form-group">
                                <label>From Ledger<span style="color:red">*</span></label>
                                <select  tabindex="1" name="from_ledger" class="form-control searchOptions" style="width: 100%">
                                    <option value="0">--Choose--</option>
                                    @foreach ($ledgers as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
    
                                @error('from_ledger')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4 journal" style="display: none;" >
                            <div class="form-group">
                                <label>To Ledger<span style="color:red">*</span></label>
                                <select  tabindex="1" name="to_ledger" class="form-control searchOptions" style="width: 100%">
                                    <option value="0">--Choose--</option>
                                    @foreach ($ledgers as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
    
                                @error('to_ledger')
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
                                    value="{{ isset($transfer) ? $transfer->amount : old('amount') }}"
                                    class="form-control">
                                @error('amount')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Date<span style="color:red">*</span></label>
                                <input  type="date" step="any" name="date"
                                    value="{{ isset($transfer) ? $transfer->date : old('date') }}"
                                    class="form-control">
                                @error('date')
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
                                    value="{{ isset($transfer) ? $transfer->remark : old('remark') }}"
                                    class="form-control">
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
    <script>
        function transferType(value) {
            if (value == "internal") {
                $('.internal-types-inputs').show();
                $('.external-types-inputs').hide()
            } 
            else if(value=='journal')
            {
                $('.journal').show();
                $('.internal-types-inputs').hide();
                $('.external-types-inputs').hide()
                $('.to-bank-input').hide();
            }
            else {
                $('.external-types-inputs').show()
                $('.internal-types-inputs').hide()
            }
        

        }

        function payemntType(value) {
            if (value == 'cash') {
                $('.to-bank-input').hide()
            } else {
                $('.to-bank-input').show()

            }

        }
    </script>
@endsection
