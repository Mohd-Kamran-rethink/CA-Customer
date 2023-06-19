@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Transaction Details</h1>
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
                <div class="mb-3 d-flex justify-content-between align-items-centers">
                    <form action="{{ url('clients/transactions/view-details') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-11 pl-0">

                        <div class="col-2 ">
                            <input name="id" type="hidden" value="{{ $id }}">
                            <label for="">From</label>
                            <input name="from_date" type="date" class="form-control from_date" id="from_date"
                                value="{{ isset($startDate) ? $startDate : '' }}">
                        </div>
                        <div class="col-2">
                            <label for="">To</label>
                            <input name="to_date" type="date" class="form-control to_date" id="to_date"
                                value="{{ isset($endDate) ? $endDate : '' }}">
                        </div>
                        <div class="col-2">
                            <label for="">Transaction Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="">--Choose--</option>
                                <option {{ isset($type) && $type == 'withdraw' ? 'selected' : '' }} value="withdraw">
                                    Withdraw
                                </option>
                                <option {{ isset($type) && $type == 'deposit' ? 'selected' : '' }} value="deposit">Deposit
                                </option>
                            </select>
                        </div>


                        <div class="">
                            <label for="" style="visibility: hidden;">filter</label>
                            <button class="btn btn-success form-control" onclick="searchData()">Filter</button>
                        </div>
                        @php
                            $totalDeposit = 0;
                            $totalWithdraw = 0;
                         @endphp
                         @foreach($activites as $act)
                         @php
                             $totalAmount = $act->amount + $act->bonus;
                             if ($act->type == 'Deposit') {
                                 $totalDeposit += $totalAmount;
                             } elseif ($act->type == 'Withdraw') {
                                 $totalWithdraw += $totalAmount;
                             }
                         @endphp
                         @endforeach
                    <div class="col-6" style="display: flex;justify-content: flex-end;flex-direction: column;align-items: flex-end">
                        <h6 class="font-weight-bold"> Total Deposit: {{$totalDeposit}}</h6>
                        <h6 class="font-weight-bold"> Total Withdraw: {{$totalWithdraw}}</h6>
                        <h6 class="font-weight-bold"> Difference: {{$totalDeposit-$totalWithdraw}}</h6>
                    </div>
                    </form>

                    <div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Bonus</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                   
                                    <tbody>
                                        @forelse($activites as $item)
                                            
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td style="text-transform: capitalize">

                                                    {{ $item->type }}
                                                </td>
                                                <td>{{ $item->amount }}</td>
                                                <td>{{ $item->bonus }}</td>
                                                <td>{{ $item->amount + $item->bonus }}</td>
                                                <td>{{ $item->created_at }}</td>

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
                    <h4 class="modal-title">Delete Franchise</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/exchanges/delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="deleteId" id="deleteInput">
                    <div class="modal-body">
                        <h4>Are you sure you want to delete this franchise?</h4>
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
        const searchData = () => {
            event.preventDefault();
            const url = new URL(window.location.href);
            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();
            const type = $('#type').val();
            url.searchParams.set('to_date', to_date);
            url.searchParams.set('from_date', from_date ?? '');
            url.searchParams.set('type', type ?? '');
            $('#search-form').attr('action', url.toString()).submit();
        }
    </script>
@endsection
