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
                <div class="mb-3 d-flex justify-content-between align-items-centers  flex-row">
                    <form action="{{ url('clients') }}" method="GET" id="search-form" class="d-flex" >
                        <div class="form-group mr-2">
                            <input value="{{isset($searchTerm)?$searchTerm:''}}" type="text" class="form-control" name="table_search" placeholder="search">
                        </div>
                        <div class="form-group">
                            <select name="filterData" id="filterData" class="form-control">
                                <option {{isset($filterData)&&$filterData=='all'?'selected':''}} value="all">All</option>
                                <option {{isset($filterData)&&$filterData=='without_agent'?'selected':''}} value="without_agent">Without Agent</option>
                            </select>
                        </div>
                            
                        <div class=" mx-2">
                            <button class="btn btn-success" onclick="searchData()">Filter</button>
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
                                            <th style="width: 5%">S.No.</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>ID Name</th>
                                            <th>Agent Name</th>
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
                                                <td>{{ session('user')->role=='customer_care_manager'?$item->number: substr($item->number, 0, -3) . str_repeat('*', 3) }}</td>
                                                <td>{{ $item->ca_id }}</td>
                                                <td>{{ $item->agent_name }}</td>
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
                                                        class="btn btn-primary">View Banks</a>
                                                    @if (!$item->agent_id && session('user')->role=='customer_care_manager')
                                                        <button onclick="openAssignLead({{$item->id}})" class="btn btn-danger">Assign Agent</button>
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
                                {{ $clients->links('pagination::bootstrap-4') }}
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
                    <h4 class="modal-title">Assign client to agent</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="{{ url('/clients/assign') }}" method="POST" class="mx-3">
                    @csrf
                    <input type="hidden" name="clientID" id="clientID">
                    <div class="form-group">
                        <label for="">Select Agent</label>
                        <select class="form-control" name="agent_id">
                            <option value="0">--Choose--</option>
                            @foreach ($agents as $item)
                            <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                                
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" class="btn btn-danger">submit</button>
                        <button type="button" data-dismiss="modal" aria-label="Close"
                            class="btn btn-default">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        const searchData = () => {
            const searchData = () => {
            event.preventDefault();
            const url = new URL(window.location.href);

            const searchValue = $('#filterData').val().trim();
            url.searchParams.set('search', searchValue);
            $('#search-form').attr('action', url.toString()).submit();
        }
        }
        function openAssignLead(id)
        {
            $('#modal-default').modal('show');
            $('#clientID').val(id);
        }
    </script>
@endsection
