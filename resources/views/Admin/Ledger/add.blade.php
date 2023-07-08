@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ isset($ledger) ? 'Edit Ledger' : 'Add Ledger' }}</h1>
                    <h6 class="text-danger">* Items marked with an asterisk are required fields and must be completed</h6>
                </div>
            </div>
            @if (session()->has('msg-success'))
                <div class="alert alert-success" role="alert">
                    {{ session('msg-success') }}
                </div>
            @elseif (session()->has('msg-error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('msg-error') }}
                </div>
            @endif
        </div>
    </section>
    <section class="content">
        <div class="card">

            <div class="card-body">
                <form action="{{ isset($ledger) ? url('ledgers/edit') : url('ledgers/add') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="hiddenid" value="{{ isset($ledger) ? $ledger->id : '' }}">
                        <div class="col-xs-12 col-md-4">
                            <div class="form-ledger">
                                <label>Name<span style="color:red">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ isset($ledger) ? $ledger->name : old('name') }}">
                                @error('name')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="col-xs-12 col-md-4">
                            <div class="form-ledger">
                                <label>Group<span style="color:red">*</span></label>
                                <select type="text" name="group" class="form-control"
                                    value="{{ isset($ledger) ? $ledger->group_id : old('group') }}">
                                    <option value="0">--Choose--</option>
                                    @foreach ($groups as $item)
                                        <option {{ isset($ledger)&&$item->id==$ledger->ledger_group?"selected":''}} value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('group')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-ledger">
                                <label for="">Amount</label>
                                <input type="text" name="amount" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-info">Save</button>
                            <a href="{{ url('/ledgers') }}" type="button" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
