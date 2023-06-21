@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Debitors </h1>
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
                <div class="card-header d-flex justify-content-between mx-0 px-0">
                    {{-- <form action="{{ url('expense-users/creditors') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-11 pl-0 mx-0">

                        <div class="col-2 ">
                            <label for="">From</label>
                            <input name="from_date" id="from_date" type="date" class="form-control"
                                value="{{ isset($startDate) ? $startDate : '' }}">
                        </div>
                        <div class="col-2">
                            <label for="">To</label>
                            <input name="to_date" id="to_date" type="date" class="form-control"
                                value="{{ isset($endDate) ? $endDate : '' }}">
                        </div>
                       

                        <div class="col-2">
                            <label for="">Transaction Type</label>
                            <select name="transaction_type" id="transaction_type" class="form-control">
                                <option value="">--Choose--</option>
                                <option value="Cash"
                                    {{ isset($transaction_type) && $transaction_type == 'Cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="Bank"
                                    {{ isset($transaction_type) && $transaction_type == 'Bank' ? 'selected' : '' }}>Bank
                                </option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="">Currency</label>
                            <select name="currency" id="currency" class="form-control">
                                <option value="">--Choose--</option>
                                <option value="rupee" {{ isset($currency) && $currency == 'rupee' ? 'selected' : '' }}>
                                    Rupee</option>
                                <option value="aed" {{ isset($currency) && $currency == 'aed' ? 'selected' : '' }}>AED
                                </option>
                            </select>

                        </div>
                        <div class="">
                            <label for="" style="visibility: hidden;">filter</label>
                            <button class="btn btn-success form-control" onclick="searchData()">Filter</button>
                        </div>
                    </form> --}}
                    <form>

                        <div>

                            <label for="" style="visibility: hidden;"> d</label>
                            <a href="{{url('expense-users/debitors/add')}}" class="btn btn-primary form-control">Add Debitor</a>
                        </div>
                    </form>

                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    <button title="Delete this expense"
                                                        onclick="manageModal({{ $item->id }})"
                                                        class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                                    @if ($item->attatchement)
                                                        <a href="{{ url('expenses/download/attatchement/' . $item->id) }}"
                                                            title="Download attatchement" class="btn btn-success"><i
                                                                class="fa fa-download"></i></a>
                                                    @endif
                                                </td>


                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No data</td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade show" id="modal-default" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete expense</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/expenses/delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="deleteId" id="deleteInput">

                    <div class="modal-body">
                        <h4>Are you sure you want to delete this expense?</h4>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function manageModal(id) {
            $('#modal-default').modal('show')
            $('#deleteInput').val(id)
        }
        const searchData = () => {
            event.preventDefault();
            const url = new URL(window.location.href);
            const currency = $('#currency').val();
            const transaction_type = $('#transaction_type').val();
            const expense_type = $('#expense_type').val();
            const to_date = $('#to_date').val();
            const from_date = $('#from_date').val();
            url.searchParams.set('from_date', from_date);
            url.searchParams.set('to_date', to_date ?? '');
            url.searchParams.set('expense_type', expense_type ?? '');
            url.searchParams.set('transaction_type', transaction_type ?? '');
            url.searchParams.set('currency', currency ?? '');
            $('#search-form').attr('action', url.toString()).submit();
        }
    </script>
@endsection
