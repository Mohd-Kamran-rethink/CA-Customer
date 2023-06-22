@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Clients</h1>
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

                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">S.No.</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>ID Name</th>
                                            <th>Last Deposit</th>
                                            <th>Last Withdraw</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($clients as $item)
                                            <tr>
                                                <td style="width: 5%">{{ $loop->iteration }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->number }}</td>
                                                <td>{{ $item->ca_id }}</td>
                                                <td>{{ $item->lastDepositDate ? $item->lastDepositDate->format('d-M-Y  h:i:s A') : 'No Deposit yet' }}
                                                    {{ $item->lastDepositDaysAgo != 0 ? $item->lastDepositDaysAgo . ' days ago' : '' }}
                                                </td>
                                                <td>{{ $item->lastWithdrawalDate ? $item->lastWithdrawalDate->format('d-M-Y  h:i:s A') : 'No Withdraw yet' }}
                                                    {{ $item->lastWithdrawalDaysAgo != 0 ? $item->lastWithdrawalDaysAgo . ' days ago' : '' }}
                                                </td>
                                                <td>
                                                    <a href="{{ url('clients/transactions/view-details?id=' . $item->id) }}"
                                                        class="btn btn-success">View Details</a>
                                                    <a href="{{ url('clients/view-banks/?id=' . $item->id) }}"
                                                        class="btn btn-success">View Banks</a>
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
                    <h4 class="modal-title">Delete user</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/user/delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="deleteId" id="deleteInput">
                    <input type="hidden" name="role" id="deleteInput" value="customer_care_manager">
                    <div class="modal-body">
                        <h4>Are you sure you want to delete this manager?</h4>
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

            const searchValue = $('#searchInput').val().trim();
            url.searchParams.set('search', searchValue);
            $('#search-form').attr('action', url.toString()).submit();
        }
    </script>
@endsection
