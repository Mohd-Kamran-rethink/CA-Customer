@extends('Admin.index')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Transfers Details</h1>
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
                <form action="{{ url('transfers') }}" method="get" class="my-4" id="search-form">
                    <div class="row">
                        <div class="col-2">
                            <label for="">From</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate??'' }}">
                        </div>
                        <div class="col-2">
                            <label for="">To</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate??'' }}">
                        </div>
                        <div class="col-2 pt-2 ">
                            <div class="row d-flex justify-content-around">
                                <button onclick="searchData()" type="button" class="btn btn-success mt-4">Filter</button>
                                <a href="{{ url('transfers/add') }}" class="btn btn-primary mt-4">Add Transfer</a>
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
                                            <th>Transfer Type</th>
                                            <th>From Bank</th>
                                            <th>To Bank</th>
                                            <th>Payment Type</th>
                                            <th>Total Amount</th>
                                            <th>Remark</th>
                                            <th>Transfer Date:Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transfers as $item)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td style="text-transform: capitalize">{{ $item->transfer_type }}</td>
                                                <td>{{ $item->bank_from??'--' }}</td>
                                                <td>{{ $item->bank_to??'--' }}</td>
                                                <td style="text-transform: capitalize">{{ $item->payment_type=='0'?'--':$item->payment_type }}</td>
                                                <td>{{ $item->amount }}</td>
                                                <td>{{ $item->remark }}</td>
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
    
    <script>
        const searchData = () => {
            event.preventDefault();
            const url = new URL(window.location.href);
            const from_date = $('#from_date').val();
            const to_date = $('#to_date').val();
            url.searchParams.set('to_date', to_date);
            url.searchParams.set('from_date', from_date ?? '');
            $('#search-form').attr('action', url.toString()).submit();
        }
    </script>
@endsection
