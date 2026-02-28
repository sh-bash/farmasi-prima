<div class="container-fluid mt-4">

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between">
            <span>Receivable Rekap</span>

            <input type="text"
                   class="form-control"
                   placeholder="Search patient..."
                   wire:model.live.debounce.300ms="search"
                   style="width:250px;">
        </div>

        <div class="card-body table-responsive">
            <input type="text"
                class="form-control"
                placeholder="Search patient..."
                wire:model.live.debounce.300ms="search"
                style="width:250px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th wire:click="sortBy('patients.name')" style="cursor:pointer;">
                            Patient
                        </th>

                        <th class="text-end"
                            wire:click="sortBy('total_invoice')" style="cursor:pointer;">
                            Total Invoice
                        </th>

                        <th class="text-end"
                            wire:click="sortBy('total_paid')" style="cursor:pointer;">
                            Total Paid
                        </th>

                        <th class="text-end"
                            wire:click="sortBy('total_outstanding')" style="cursor:pointer;">
                            Outstanding
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                        <tr>
                            <td>
                                <a href="{{ route('report.receivable.detail', $row->id) }}">
                                    {{ $row->name }}
                                </a>
                            </td>
                            <td class="text-end">
                                {{ number_format($row->total_invoice,0) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($row->total_paid,0) }}
                            </td>
                            <td class="text-end fw-bold text-danger">
                                {{ number_format($row->total_outstanding,0) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $data->links() }}
        </div>
    </div>

</div>