@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Bank Transaction Details</h1>
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
                    <form action="{{ url('reports/deposits') }}" method="GET" id="search-form"
                        class="filters d-flex flex-row col-11 pl-0">

                        <div class="col-3 ">
                            <input name="id" type="hidden" value="{{ $id }}">
                            <label for="">From</label>
                            <input name="from_date" type="date" class="form-control from_date" id="from_date"
                                value="{{ isset($startDate) ? $startDate : '' }}">
                        </div>
                        <div class="col-3">
                            <label for="">To</label>
                            <input name="to_date" type="date" class="form-control to_date" id="to_date"
                                value="{{ isset($endDate) ? $endDate : '' }}">
                        </div>
                        <div class="col-3">
                            <label for="">Transaction Type</label>
                            <select class="form-control" name="type" id="type">
                                <option value="">--Choose--</option>
                                <option {{ isset($type) && $type == 'withdraw' ? 'selected' : '' }} value="withdraw">Withdraw
                                </option>
                                <option {{ isset($type) && $type == 'deposit' ? 'selected' : '' }} value="deposit">Deposit</option>
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="">Client</label>
                            <select class="form-control" name="client_id" id="client_id">
                                <option value="null">--Choose--</option>
                                @foreach ($clients as $item)
                                    <option {{ isset($client_id) && $client_id == $item->id ? 'selected' : '' }}
                                        value="{{ $item->id }}">{{ $item->name??''}}-{{$item->ca_id??''}}-{{$item->number??''}}</option>
                                @endforeach
                            </select>

                        </div>

                        <div class="">
                            <label for="" style="visibility: hidden;">filter</label>
                            <button class="btn btn-success form-control" onclick="searchData()">Filter</button>
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
                                            <th>Client ID</th>
                                            <th>Approved By</th>
                                            <th>Type</th>
                                            <th>Total Amount</th>
                                            <th>Opening Balance</th>
                                            <th>Current Balance</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $item->client_name }}</td>
                                                <td>{{ $item->approved_by }}</td>
                                                <td style="text-transform: capitalize">
                                                   {{ $item->type }}
                                                </td>
                                                <td>{{ $item->amount }}</td>
                                                <td>{{ $item->opening_balance }}</td>
                                                <td>{{ $item->current_balance }}</td>
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
            const client_id = $('#client_id').val();
            url.searchParams.set('to_date', to_date);
            url.searchParams.set('from_date', from_date ?? '');
            url.searchParams.set('type', type ?? '');
            url.searchParams.set('client_id', client_id ?? '');
            $('#search-form').attr('action', url.toString()).submit();
        }
    </script>
@endsection
