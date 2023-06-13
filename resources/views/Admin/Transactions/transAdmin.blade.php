@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ $heading ?? '' }}</h1>
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
                {{-- <form action="{{ url('dashboard') }}" method="POST">
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
                </form> --}}
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

                                                    <button title="Delete" onclick="deleteModal({{ $item->id }})"
                                                        class="btn btn-danger"><i class="fa fa-trash"></i></button>

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
                                {{-- {{ $transactions->links('pagination::bootstrap-4') }} --}}
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
    <script>
        function deleteModal(id) {
            $('#modal-default').modal('show')
            $('#deleteInput').val(id)
        }
    </script>
@endsection
