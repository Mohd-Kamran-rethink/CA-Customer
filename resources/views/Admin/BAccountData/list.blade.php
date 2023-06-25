@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bank Details</h1>
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
                    <form action="{{ url('banks') }}" method="GET" id="search-form">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" value="{{ isset($searchTerm) ? $searchTerm : '' }}" name="table_search"
                                class="form-control float-right" placeholder="Search" id="searchInput">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default" onclick="searchData()" id="search-button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div>
                        <a href="{{ url('bank-accounts/add') }}" class="btn btn-primary">Add Bank Detail</a>
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
                                            <th>Bank</th>
                                            <th>Opening Balance</th>
                                            <th>Total Deposit</th>
                                            <th>Total Withdraw</th>
                                            <th>Closing Balance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($banks as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>[{{ $item->holder_name }}]-[{{ $item->bank_name }}]-[{{ $item->account_number }}]-[{{ $item->ifsc }}]</td>
                                                <td>{{ $item->closginYesterday??'--' }}</td>
                                                <td>{{ $item->totalDeposit??0 }}</td>
                                                <td>{{ $item->totalWithdraw??0 }}</td>
                                                <td>{{ $item->amount??0 }}</td>
                                                <td>
                                                    {{-- <a href="{{ url('bank-accounts/deposit-money/' . $item->id) }}"
                                                        title="Deposit money" class="btn btn-primary">Deposit</a>
                                                    <a href="{{ url('bank-accounts/withdraw-money/' . $item->id) }}"
                                                        title="withdraw" class="btn btn-secondary">Withdraw</a> --}}


                                                    @if (session('user')->role == 'deposit_banker' ||
                                                            session('user')->role == 'withdrawal_banker' ||
                                                            session('user')->role == 'customer_care_manager')
                                                        <a href="{{ url('bank-accounts/details/?id=' . $item->id) }}"
                                                            title="View Transaction details" class="btn btn-success">View
                                                            Details</a>
                                                        <a href="{{ url('bank-accounts/edit/' . $item->id) }}"
                                                            title="Edit" class="btn btn-primary"><i
                                                                class="fa fa-pen"></i></a>
                                                        @if ($item->is_active == 'Yes')
                                                            <button title="Delete"
                                                                onclick="manageModal({{ $item->id }})"
                                                                class="btn btn-danger">Deactivate</button>
                                                        @else
                                                            <button title="Delete" onclick="reactive({{ $item->id }})"
                                                                class="btn btn-success">Activate</button>
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
                                {{ $banks->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade show" id="modal-default" style=" padding-right: 17px;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Delete Banks Detail</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="{{ url('/bank-accounts/delete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="deleteId" id="deleteInput">
                        <div class="modal-body">
                            <h4>Are you sure you want to delete this Banks Detail?</h4>
                        </div>
                        <div class="modal-footer ">
                            <button type="submit" class="btn btn-danger">Delete</button>
                            <button type="button" data-dismiss="modal" aria-label="Close"
                                class="btn btn-default">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    {{-- reacticve --}}
    <div class="modal fade show" id="modal-reactive" style=" padding-right: 17px;" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Activate Bank</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/bank-accounts/reactive') }}" method="POST">
                    @csrf
                    <input type="hidden" name="deleteId" id="reactivateId">
                    <div class="modal-body">
                        <h4>Are you sure you want to activate this Bank?</h4>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" class="btn btn-danger">Yes</button>
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

            const searchValue = $('#searchInput').val().trim();
            url.searchParams.set('search', searchValue);
            $('#search-form').attr('action', url.toString()).submit();
        }

        function reactive(id) {
            $('#modal-reactive').modal('show')
            $('#reactivateId').val(id)
        }
    </script>
@endsection
