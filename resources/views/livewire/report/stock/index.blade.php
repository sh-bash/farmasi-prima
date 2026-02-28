<div class="container-fluid mt-4">

    <div class="card shadow-sm">
        <div class="card-header">
            Stock Rekap
        </div>

        <div class="card-body table-responsive">
            <div class="d-flex justify-content-between align-items-center mb-3">

                <div>
                    <input type="text"
                           class="form-control"
                           placeholder="Search product..."
                           wire:model.live.debounce.500ms="search"
                           style="width: 250px;">
                </div>

            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th wire:click="sortBy('p.name')" style="cursor:pointer;">Product</th>
                        <th wire:click="sortBy('stock_in')" style="cursor:pointer;">Stock In</th>
                        <th wire:click="sortBy('stock_out')" style="cursor:pointer;">Stock Out</th>
                        <th wire:click="sortBy('current_stock')" style="cursor:pointer;">Current Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <tbody>
                        @foreach($data as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('report.stock.detail', $row->id) }}">
                                        {{ $row->name }}
                                    </a>
                                </td>

                                <td class="text-end">
                                    {{ number_format($row->stock_in, 0) }}
                                </td>

                                <td class="text-end">
                                    {{ number_format($row->stock_out, 0) }}
                                </td>

                                <td class="text-end fw-bold">
                                    {{ number_format($row->current_stock ?? ($row->stock_in - $row->stock_out), 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </tbody>
            </table>

            {{ $data->links() }}
        </div>
    </div>

</div>