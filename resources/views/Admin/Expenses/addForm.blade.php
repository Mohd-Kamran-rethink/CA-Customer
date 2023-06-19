@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add Expense</h1>
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
                <form action="{{ url('expenses/add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Department Type<span style="color:red">*</span></label>
                                <select type="text" name="department"  class="form-control">
                                    <option value="0">--Choose--</option>
                                    @foreach ($departments as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>    
                                    @endforeach
                                </select>
                                @error('department')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Expense Type<span style="color:red">*</span></label>
                                <select type="text" name="expense_type"  class="form-control">
                                    <option value="0">--Choose--</option>
                                    @foreach ($expenseType as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>    
                                    @endforeach
                                </select>
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Transaction Type<span style="color:red">*</span></label>
                                <select type="text" name="transaction_type"  class="form-control">
                                    <option value="0">--Choose--</option>
                                    <option value="Bank">Bank</option>
                                    <option value="Cash">Cash</option>
                                </select>
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Banks<span style="color:red">*</span></label>
                                <select type="text" name="bank"  class="form-control">
                                    <option value="0">--Choose--</option>
                                    @foreach ($banks as $item)
                                        <option value="{{$item->id}}">{{$item->account_number.' - ' .' ( '.$item->holder_name. ')' }}</option>    
                                    @endforeach
                                </select>
                                @error('bank')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>          
                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Amount<span style="color:red">*</span></label>
                                <input type="number" step="any" name="amount"
                                    value="{{old('amount')}}"
                                    id="phone" class="form-control" data-validation="required">
                                @error('amount')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-4">
                            <div class="form-group">
                                <label>Remark<span style="color:red">*</span></label>
                                <input type="number" name="remark"
                                    value="{{old('remark')}}"
                                    id="remark" class="form-control" data-validation="required">
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
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/franchises') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
