@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard</h1>
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
    @if (session('user')->role != 'customer_care_manager')
        @if (isset($transactions))
            @php
                $sum = 0;
            @endphp
            @foreach ($transactions as $transaction)
                @php
                    $type = $transaction->type;
                    $sum += $transaction->total;
                @endphp
            @endforeach
        @endif
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ isset($transactions) ? count($transactions) ?? 0 : 0 }} </h3>
                                <p>Transactions</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    {{-- for withdraw --}}
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ number_format($sum) }}</h3>
                                @if (session('user')->role == 'withdrawal_banker' || session('user')->role == 'withdrawrer')
                                    <p>Withdraws</p>
                                @elseif(session('user')->role == 'deposit_banker' || session('user')->role == 'depositer')
                                    <p>Deposits</p>
                                @endif
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </section>
    @endif
    {{-- cards --}}
    {{-- transactions for deposit_banker --}}
    @if (session('user')->role != 'customer_care_manager')
        <section class="content">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('dashboard') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-2">
                                <label for="" style="visibility: hidden">s</label>
                                <input type="text" value="{{ isset($search) ? $search : '' }}" name="table_search"
                                    class="form-control float-right" placeholder="Search by UTR" id="searchInput">
                            </div>
                            <div class="col-2">
                                <label for="" style="visibility: hidden">s</label>
                                <input type="text" value="{{ isset($amount_search) ? $amount_search : '' }}"
                                    name="amount_search" class="form-control float-right" placeholder="Search by amount"
                                    id="searchInput">
                            </div>
                            <div class="col-2 ">
                                <label for="" style="visibility: hidden">sdf</label>
                                <select name="status_name" type="text" class="form-control">
                                    <option value="null">--Filter by status--</option>
                                    <option {{ $status == 'Approve' ? 'selected' : '' }} value="Approve">Approved</option>
                                    <option {{ $status == 'Cancel' ? 'selected' : '' }} value="Cancel">Canceled</option>
                                    <option {{ $status == 'Pending' ? 'selected' : '' }} value="Pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-2">
                                <label for="">From</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                            </div>
                            <div class="col-2">
                                <label for="">To</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                            </div>
                            <div class="col-2 pt-2">
                                <button class="btn btn-success mt-4">Filter</button>
                            </div>
                        </div>
                    </form>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Amount</th>
                                                <th>Bonus</th>
                                                <th>Total</th>
                                                <th>Date</th>
                                                <th>Bank Account</th>
                                                <th>UTR No.</th>
                                                <th>Status</th>
                                                <th>Created On</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transactions as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->amount }}</td>
                                                    <td>{{ $item->bonus }}</td>
                                                    <td>{{ $item->total }}</td>
                                                    <td>{{ $item->date }}</td>
                                                    <td>{{ $item->holder_name }}</td>
                                                    <td>{{ $item->utr_no }}</td>
                                                    <td>{{ $item->status }}</td>
                                                    <td>{{ $item->created_at }}</td>
                                                    <td>
                                                        {{-- for deposit functionlaity --}}
                                                        @if (session('user')->role === 'deposit_banker' && $item->status == 'Pending')
                                                            <a href="{{ url('transactions/edit/' . $item->id) }}"
                                                                title="Edit" class="btn btn-primary"><i
                                                                    class="fa fa-pen"></i></a>
                                                            <button title="Delete"
                                                                onclick="deleteModal({{ $item->id }})"
                                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                                        @elseif(session('user')->role === 'depositer' && $item->status == 'Pending')
                                                            <a href="{{ url('transactions/change-status/' . $item->id) }}"
                                                                title="Change Status" class="btn btn-primary">Change
                                                                Status</a>
                                                        @endif
                                                        {{-- for withdrawal functionality --}}
                                                        @if (session('user')->role === 'withdrawrer' && $item->status == 'Pending')
                                                            <a href="{{ url('transactions/withdraw/edit/' . $item->id) }}"
                                                                title="Edit" class="btn btn-primary"><i
                                                                    class="fa fa-pen"></i></a>
                                                            <button title="Delete"
                                                                onclick="deleteModal({{ $item->id }})"
                                                                class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                                        @elseif(session('user')->role === 'withdrawal_banker' && $item->status == 'Pending')
                                                            <a href="{{ url('transactions/change-status-withdraw/' . $item->id) }}"
                                                                title="Change Status" class="btn btn-primary">Change
                                                                Status</a>
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
                                <div class="card-footer clearfix">
                                    {{ $transactions->links('pagination::bootstrap-4') }}
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
                        <h4 class="modal-title">Delete transaction</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ url('/transactions/delete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="deleteId" id="deleteInput">
                        <div class="modal-body">
                            <h4>Are you sure you want to delete this transaction?</h4>
                        </div>
                        <div class="modal-footer ">
                            <button type="submit" class="btn btn-danger">Delete</button>
                            <button type="button" data-dismiss="modal" aria-label="Close"
                                class="btn btn-default">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <script>
        function deleteModal(id) {
            $('#modal-default').modal('show');
            $('#deleteInput').val(id);
        }
    </script>
    {{-- for customercar manager --}}
    @if (session('user')->role == 'customer_care_manager')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $depositBanker ?? 0 }} </h3>
                                <p>Deposit Bankers</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                    {{--  --}}
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $depositers ?? 0 }} </h3>
                                <p>Depositers</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $withdraweres ?? 0 }} </h3>
                                <p>Withdrawrers</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $withdrawrerBanker ?? 0 }} </h3>
                                <p>Withdrawal Bankers</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $PendingDepoistTranTotal ?? 0 }} </h3>
                                <p>Pending Deposit</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $PendinhwithTranTotal ?? 0 }} </h3>
                                <p>Pending Withdrawal</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- daily --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Financial Report (Daily)</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h4>{{ $ApprovedDepoistToday ?? 0 }} </h4>
                                <p>Total Todays's Deposit</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h4>{{ $ApprovedWithdrawToday ?? 0 }} </h4>
                                <p>Total Todays's Withdaws</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h4>{{ $todaysBonus ?? 0 }} </h4>
                                <p>Total Todays's Bonus</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {{-- total --}}
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Financial Report (Total)</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h4>{{ $ApprovedDepoistTotal ?? 0 }} </h4>
                                <p>Total Deposit</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h4>{{ $ApprovedWithdrawTotal ?? 0 }} </h4>
                                <p>Total Withdaws</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h4>{{ $totalBonus ?? 0 }} </h4>
                                <p>Total bonus</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
