<div class="container-fluid mt-4">

    {{-- ================= PANEL 1 : FILTER ================= --}}
    <div class="card mb-4 shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white"
            wire:click="toggleFilter"
            style="cursor:pointer;">

            <span>Filter Report Purchase</span>

            <i class="bi {{ $isFilterOpen ? 'bi-chevron-down' : 'bi-chevron-right' }}"></i>
        </div>

        <div class="collapse {{ $isFilterOpen ? 'show' : '' }}">
            <div class="card-body row">

                <div class="col-md-3 mb-3">
                    <label>Date From</label>
                    <input type="date" class="form-control"
                           wire:model.defer="date_from">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Date To</label>
                    <input type="date" class="form-control"
                           wire:model.defer="date_to">
                </div>

                <div class="col-md-3 mb-3">
                    <label>Supplier</label>
                    <select class="form-control"
                            wire:model.defer="supplier_id">
                        <option value="">-- All Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label>Status</label>
                    <select class="form-control"
                            wire:model.defer="status">
                        <option value="">-- All Status --</option>
                        <option value="posted">Posted</option>
                        <option value="paid">Paid</option>
                        <option value="partial">Partial</option>
                    </select>
                </div>

                {{-- SELECT2 --}}
                <div class="col-md-6 mb-3" wire:ignore>
                    <label>Product (Multi Select)</label>
                    <select id="productSelect" class="form-control" multiple>
                        @foreach($allProducts as $prod)
                            <option value="{{ $prod->id }}">
                                {{ $prod->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-primary"
                            wire:click="applyFilter">
                        Apply Filter
                    </button>
                </div>

            </div>
        </div>
    </div>


    {{-- ================= PANEL 2 : TABLE ================= --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Purchase Report Data</span>
            <button class="btn btn-success btn-sm" wire:click="export">
                Export Excel
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Invoice</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        @foreach($row->details as $detail)
                            <tr>
                                <td>{{ date('d F Y', strtotime($row->purchase_date)) }}</td>
                                <td>{{ $row->purchase_number }}</td>
                                <td>{{ $row->supplier->name ?? '-' }}</td>
                                <td>{{ ucfirst($row->status) }}</td>
                                <td>{{ $detail->product->name ?? '-' }}</td>
                                <td>{{ number_format($detail->qty) }}</td>
                                <td>{{ number_format($detail->price) }}</td>
                                <td>{{ number_format($detail->total) }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $data->links() }}
        </div>
    </div>

</div>

@push('scripts')

<script>
document.addEventListener('livewire:init', function () {

    $('#productSelect').select2({
        placeholder: "Select product",
        width: '100%'
    });

    $('#productSelect').on('change', function () {
        @this.set('products', $(this).val());
    });

});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const collapse = document.getElementById('filterCollapse');
        const icon = document.getElementById('filterIcon');

        collapse.addEventListener('show.bs.collapse', function () {
            icon.classList.remove('bi-chevron-right');
            icon.classList.add('bi-chevron-down');
        });

        collapse.addEventListener('hide.bs.collapse', function () {
            icon.classList.remove('bi-chevron-down');
            icon.classList.add('bi-chevron-right');
        });

    });
</script>
@endpush