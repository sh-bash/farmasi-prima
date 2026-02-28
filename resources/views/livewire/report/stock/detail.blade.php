<div class="container-fluid mt-4">
    <div class="card mb-3 shadow-sm">
        <div class="card-body">

            <div class="row">
                <div class="col-md-4">
                    <strong>Product Name</strong><br>
                    {{ $product->name }}
                </div>

                <div class="col-md-4">
                    <strong>Product Code</strong><br>
                    {{ $product->barcode ?? '-' }}
                </div>

                <div class="col-md-4">
                    <strong>Current Stock</strong><br>
                    <span class="badge bg-primary fs-6">
                        {{ number_format($currentStock) }}
                    </span>
                </div>
            </div>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            Stock History
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Reference</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Balance</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($data as $row)
                    <tr>
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->type }}</td>
                        <td>{{ $row->reference }}</td>
                        <td>{{ number_format($row->stock_in) }}</td>
                        <td>{{ number_format($row->stock_out) }}</td>
                        <td>
                            <strong>{{ number_format($row->balance) }}</strong>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            {{ $data->links() }}
        </div>
    </div>

</div>