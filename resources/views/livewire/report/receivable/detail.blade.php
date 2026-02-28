<div class="container-fluid mt-4">

    <div class="card mb-3">
        <div class="card-body">
            <strong>Patient:</strong> {{ $patient->name }}
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $row->sale_date }}</td>

                            <td>
                                <a href="{{ route('transaction.sales.show', $row->id) }}"
                                   class="fw-semibold text-primary">
                                    {{ $row->sale_number }}
                                </a>
                            </td>

                            <td class="text-end">
                                {{ number_format($row->grand_total,0) }}
                            </td>

                            <td class="text-end">
                                {{ number_format($row->paid_total,0) }}
                            </td>

                            <td class="text-end text-danger fw-bold">
                                {{ number_format($row->balance,0) }}
                            </td>

                            <td>{{ ucfirst($row->status) }}</td>

                            <td class="text-center">
                                <a href="{{ route('transaction.sales.payment', $row->id) }}"
                                   class="btn btn-sm btn-success">
                                    Make Payment
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $data->links() }}
        </div>
    </div>

</div>