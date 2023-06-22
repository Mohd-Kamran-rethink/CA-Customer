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
                {{-- <div class="mb-3 d-flex justify-content-between align-items-centers">
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
                    
                </div> --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Account Holder Name</th>
                                            <th>Bank Account</th>
                                            <th>Account Number</th>
                                            <th>IFSC Code</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($banks as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->holder_name }}</td>
                                                <td>{{ $item->bank_name }}</td>
                                                <td>{{ $item->account_number }}</td>
                                                <td>{{ $item->ifsc }}</td>
                                                <td>
                                                    {{-- <a href="{{ url('bank-accounts/deposit-money/' . $item->id) }}"
                                                        title="Deposit money" class="btn btn-primary">Deposit</a> --}}
                                                    <a href="{{ url('client/bank-accounts/edit/' . $item->id) }}"
                                                        title="withdraw" class="btn btn-secondary">Edit</a>


                                                    @if (session('user')->role == 'deposit_banker' ||
                                                            session('user')->role == 'withdrawal_banker' ||
                                                            session('user')->role == 'customer_care_manager')
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
