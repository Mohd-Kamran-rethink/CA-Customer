@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Ledger Details</h1>
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
                <form action="{{ url('ledgers/view-details?id=' . $id) }}" method="post" class="my-4">
                    @csrf
                    <div class="row">
                        <div class="col-2">
                            <label for="">From</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? '' }}">
                        </div>
                        <div class="col-2">
                            <label for="">To</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? '' }}">
                        </div>
                        <div class="col-2 pt-2 ">
                            <div class="row d-flex justify-content-around">
                                <button type="submit" class="btn btn-success mt-4">Filter</button>

                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Remark</th>
                                            <th>Type</th>
                                            <th>From Ledger</th>
                                            <th>To Ledger</th>
                                            <th>Amount Transfer</th>
                                            <th>Opening Balance</th>
                                            <th>Closing Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($details as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->remark }}</td>
                                                <td>{{ $item->type }}</td>
                                                <td>{{ $item->fromLedger }}</td>
                                                <td>{{ $item->toLedger }}</td>
                                                <td>{{ $item->amount }}</td>
                                                <td>{{ $item->opening_balance }}</td>
                                                <td>{{ $item->closing_balance }}</td>
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
                                {{ $details->links('pagination::bootstrap-4') }}
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
                    <h4 class="modal-title">Delete Group</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/ledgers/delete') }}" method="POST">
                    @csrf
                    <input type="hidden" name="deleteId" id="deleteInput">
                    <div class="modal-body">
                        <h4>Are you sure you want to delete this group?</h4>
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
