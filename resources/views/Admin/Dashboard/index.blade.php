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
        @if (isset($totalApprovedForAgent))
            @php
                $sum = 0;
            @endphp
            @foreach ($totalApprovedForAgent as $transaction)
                @php
                    $type = $transaction->type;
                    $sum += $transaction->amount;
                @endphp
            @endforeach
        @endif
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ isset($totalApprovedForAgent) ? count($totalApprovedForAgent) ?? 0 : 0 }} </h3>
                                <p>Today's Pending Transactions</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    @if (session('user')->role == 'deposit_banker' || session('user')->role == 'withdrawal_banker')
                        <div class="col-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ isset($todaysApproed) ? count($todaysApproed) ?? 0 : 0 }} </h3>
                                    <p>Today's Approved Transactions</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-credit-card"></i>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- for withdraw --}}
                    <div class="col-3">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ number_format($sum) }}</h3>
                                @if (session('user')->role == 'withdrawal_banker' || session('user')->role == 'withdrawrer')
                                    <p>Today's Withdraws</p>
                                @elseif(session('user')->role == 'deposit_banker' || session('user')->role == 'depositer')
                                    <p>Today's Deposits</p>
                                @endif
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    @if(session('user')->role=='withdrawrer')
                    <div class="col-3">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ isset($totalWithdrawRevert) ? count($totalWithdrawRevert) ?? 0 : 0 }} </h3>
                                <p>Total Reverts</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="col-6 d-flex justify-content-end align-items-end pb-2">
                        <h1 id="timer">Page will reload in 10 s</h1>
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
                            <div class="col-1">
                                <label for="" style="visibility: hidden">s</label>
                                <input type="text" value="{{ isset($amount_search) ? $amount_search : '' }}"
                                    name="amount_search" class="form-control float-right" placeholder="Search by amount"
                                    id="searchInput">
                            </div>
                            <div class="col-2 ">
                                <label for="" style="visibility: hidden">sdf</label>
                                <select {{-- {{ session('user')->role === 'deposit_banker' || session('user')->role === 'withdrawrer' ? '' : 'disabled' }} --}} name="status_name" type="text" class="form-control">
                                    <option value="null">--Filter by status--</option>
                                    <option {{ $status == 'Approve' ? 'selected' : '' }} value="Approve">Approved</option>
                                    <option {{ $status == 'Cancel' ? 'selected' : '' }} value="Cancel">Canceled</option>
                                    <option {{ $status == 'Pending' ? 'selected' : '' }} value="Pending">Pending</option>
                                    @if(session('user')->role=='withdrawrer')
                                        <option {{ $status == 'Revert' ? 'selected' : '' }} value="Revert">Revert</option>
                                    @endif
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
                            <div class="col-2">
                                <label for="">Sort Amount</label>
                                <select name="sortamount" id="sortamount" class="form-control">
                                    <option value="null">--Choose--</option>
                                    <option value="asc">Ascending</option>
                                    <option value="desc">Descending</option>
                                </select>
                            </div>
                            <div class="col-1 pt-2 ">
                                <div class="row d-flex justify-content-around">
                                    <button class="btn btn-success mt-4">Filter</button>
                                    @if (session('user')->role == 'deposit_banker')
                                        <a href="{{ url('transactions/add') }}" class="btn btn-primary mt-4">Add</a>
                                    @endif

                                </div>

                            </div>
                        </div>
                    </form>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap ">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Date</th>
                                                <th>Bank Account</th>
                                                <th>UTR No.</th>
                                                <th>Amount</th>
                                                <th>Bonus</th>
                                                <th>Total</th>
                                                <th>Client ID</th>
                                                <th>Status</th>
                                                <th>Created On</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($transactions as $item)
                                                <tr>
                                                    <input type="hidden" value="{{ $item->id }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                                    <td>{{ $item->holder_name }}</td>
                                                    <td>{{ $item->utr_no }}</td>
                                                    <td>{{ $item->amount }}</td>
                                                    <td>{{ $item->bonus }}</td>
                                                    <td>{{ $item->total }}</td>
                                                    <td>{{ $item->client_id ?? '' }}</td>
                                                    <td>{{ $item->status }}</td>
                                                    <td>{{ $item->created_at }}</td>
                                                    <td>
                                                        {{-- for deposit functionlaity --}}
                                                        @if (session('user')->role === 'deposit_banker' && $item->status == 'Pending')
                                                            <a href="{{ url('transactions/edit/' . $item->id) }}"
                                                                title="Edit" class="btn btn-primary"><i
                                                                    class="fa fa-pen"></i></a>
                                                            {{-- @if ($item->status == 'Approve')
                                                                <button onclick="cancelDeposit({{ $item->id }})"
                                                                    title="Change Status" class="btn btn-danger"
                                                                    type="button">Cancel</button>
                                                            @endif --}}
                                                        @elseif(session('user')->role === 'depositer' && $item->status == 'Pending')
                                                            <a href="{{ url('transactions/change-status/' . $item->id) }}"
                                                                title="Change Status" class="btn btn-primary">Change
                                                                Status</a>
                                                        @endif
                                                        {{-- for withdrawal functionality --}}
                                                        @if (session('user')->role === 'withdrawrer')
                                                            @if ($item->status == 'Revert')
                                                            <button onclick="WithdrawCancel({{$item->id}})"
                                                                title="Edit" class="btn btn-primary">Cancel</button>
                                                            @endif
                                                            {{-- @if ($item->status == 'Approve')
                                                                <button onclick="cancelDeposit({{ $item->id }})"
                                                                    title="Change Status" class="btn btn-danger"
                                                                    type="button">Cancel</button>
                                                            @endif --}}
                                                        @elseif(session('user')->role === 'withdrawal_banker')
                                                            @if ($item->status == 'Pending')
                                                                <a href="{{ url('transactions/change-status-withdraw/' . $item->id) }}"
                                                                    title="Change Status" class="btn btn-primary">Change
                                                                    Status</a>
                                                            @endif
                                                            @if ($item->status == 'Approve')
                                                                <a href="{{ url('transactions/withdraw-banker/edit/' . $item->id) }}"
                                                                    title="Edit" class="btn btn-danger"><i
                                                                        class="fa fa-pen"></i></a>
                                                                <button onclick="revertWithdraw({{$item->id}})" title="revert"
                                                                    class="btn btn-danger">Revert</button>
                                                            @endif
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
            {{-- cancel-withdraw --}}
        <div class="modal fade show" id="cancel-withdraw" style=" padding-right: 17px;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Cancel transaction</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ url('/transactions/withdraw/cancel-the-revert') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cancelId" id="calcelWithdraw">
                        <div class="modal-body">
                            <h4>Are you sure you want to cancel this transaction?</h4>
                            
                        </div>
                        <div class="modal-footer ">
                            <button type="submit" class="btn btn-danger">Yes</button>
                            <button type="button" data-dismiss="modal" aria-label="Close"
                                class="btn btn-default">No</button>
                    </form>
                </div>
            </div>
        </div>
        </section>
        {{-- revert transaction --}}
        <div class="modal fade show" id="revert-transaction" style=" padding-right: 17px;" aria-modal="true"
            role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>Are you sure you want to revert this transaction?</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ url('/transactions/withdraw-banker/revert') }}" method="POST"
                        id="statsus-change-form">
                        @csrf
                        <input type="hidden" name="hiddenId" id="revertId">
                        <div class="modal-body">
                            <label for="">Revert Note <span style="color:red">*</span></label>
                            <textarea name="cancel_note" id="cancel_note" cols="30" rows="3" class="form-control"
                                placeholder="Write something"></textarea>
                            <span style="color:red;display: none" id="cancel_note_error">Please write revert note!</span>
                        </div>
                        <div class="modal-footer ">
                            <button onclick="submitBankerRevert()" type="submit" id="submit-button"
                                class="btn btn-danger">Submit</button>
                            <button type="button" data-dismiss="modal" aria-label="Close"
                                class="btn btn-default">Close</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- cancel deposit --}}
        <div class="modal fade show" id="depositer-cancel" style=" padding-right: 17px;" aria-modal="true"
            role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Cancel transaction</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form
                        action="{{ session('user')->role == 'withdrawrer' ? url('transactions/withdrawrer/recancel') : url('transactions/depositer/recancel') }}"
                        method="POST">
                        @csrf
                        <input type="hidden" name="transID" id="transID">
                        <div class="modal-body">
                            <h4>Are you sure you want to cancel this transaction?</h4>
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

        function cancelDeposit(id) {
            $('#depositer-cancel').modal('show')
            $('#transID').val(id)
        }

        function revertWithdraw(id) {
            $('#revert-transaction').modal('show');
            $('#revertId').val(id);
        }
        function WithdrawCancel(id)
        {
            $('#cancel-withdraw').modal('show');
            $('#calcelWithdraw').val(id);
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
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ count($PendingDepoistTranTotal) ?? 0 }} </h3>
                                <p>Pending Deposit</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ count($PendinhwithTranTotal) ?? 0 }} </h3>
                                <p>Pending Withdrawal</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                    {{-- clients --}}
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $clients ?? 0 }} </h3>
                                <p>Total Clients</p>
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

    @php
        $url = '';
        $addUrl = '';
        if (session('user')->role == 'withdrawal_banker') {
            $url = 'http://customer.cricadda.tech/transactions/change-status-withdraw/';
        } else {
            $url = 'http://customer.cricadda.tech/transactions/change-status/';
        }
        if (session('user')->role == 'deposit_banker') {
            $addUrl = 'http://customer.cricadda.tech/transactions/add';
        }
        if (session('user')->role == 'withdrawrer') {
            $addUrl = 'http://customer.cricadda.tech/transactions/withdraw/add';
        }
        
    @endphp


    {{-- timer work --}}
    <script>
        // Function to update the timer
        function updateTimer() {
            var countdown = 10; // Set the countdown duration in seconds
            var timerElement = document.getElementById("timer");

            var countdownInterval = setInterval(function() {
                timerElement.textContent = "Page will reload in " + countdown + " s"; // Update the timer display

                if (countdown === 0) {
                    clearInterval(countdownInterval);
                    location.reload(); // Reload the page when countdown reaches 0
                }

                countdown--;
            }, 1000); // 1000 milliseconds = 1 second
        }

        // Call the function to start the timer if the user role is not "manager"
        @if (session('user')->role != 'customer_care_manager')
            window.onload = updateTimer;
        @endif
    </script>

    <script>
        document.addEventListener('keydown', function(event) {
            // Check if the key pressed is the desired shortcut (e.g., "Ctrl + Alt + C")
            if (event.altKey && event.key === 'c') {
                // Call the openClientModel function
                var input = document.querySelector('tbody tr:first-child input[type="hidden"]');
                var url = '{{ $url }}';
                var newUrl = url + input.value;
                if (input && url) {

                    window.location.href = newUrl;
                }

            }
            if (event.altKey && event.key === 'a') {
                // Call the openClientModel function
                var addUrl = '{{ $addUrl }}';
                window.location.href = addUrl;


            }
            if (event.key === 'Escape') {
                $('#client-modal').modal('hide');
            }
        });
    </script>
@endsection
