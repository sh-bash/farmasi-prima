<div class="container mt-4">

    {{-- Button Add --}}
    <div class="mb-3">
        <a href="{{ route('transaction.purchases.create') }}"
           class="btn btn-primary">
            + Add Purchase
        </a>
    </div>

    {{-- Search --}}
    <div class="mb-2">
        <input type="text"
               class="form-control"
               placeholder="Search purchase..."
               wire:model.live="search">
    </div>

    {{-- Table Block --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Purchase List</h3>

            <div class="block-options">

                {{-- Refresh --}}
                <button type="button"
                        class="btn-block-option"
                        wire:click.stop.prevent="refreshData"
                        title="Refresh">
                    <i class="si si-refresh"></i>
                </button>

                {{-- Hide / Show --}}
                <button type="button"
                        class="btn-block-option"
                        wire:click.stop.prevent="toggleTable"
                        title="Hide / Show">
                    <i class="si {{ $showTable ? 'si-arrow-up' : 'si-arrow-down' }}"></i>
                </button>

            </div>
        </div>

        <div class="block-content" @if(!$showTable) style="display:none;" @endif>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="150">Action</th>
                        <th>Number</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Grand Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th width="160">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('transaction.purchases.show', $p->id) }}"
                                        class="btn btn-sm btn-alt-info">
                                         <i class="fa fa-eye"></i>
                                     </a>

                                    @if($p->status !== 'paid')
                                        <a href="{{ route('transaction.purchases.edit', $p->id) }}"
                                           class="btn btn-sm btn-alt-warning"
                                           title="Edit">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>

                                        <a href="{{ route('transaction.purchases.payment', $p->id) }}"
                                           class="btn btn-sm btn-alt-info"
                                           title="Payment">
                                            <i class="fa fa-money-bill"></i>
                                        </a>
                                    @endif

                                    <button class="btn btn-sm btn-alt-danger"
                                            wire:click="confirmDelete({{ $p->id }})"
                                            title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>

                            <td>{{ $p->purchase_number }}</td>

                            <td>{{ \Carbon\Carbon::parse($p->purchase_date)->format('d M Y') }}</td>

                            <td>{{ $p->supplier->name ?? '-' }}</td>

                            <td>{{ number_format($p->grand_total, 0) }}</td>
                            <td>{{ number_format($p->paid_total, 0) }}</td>
                            <td>{{ number_format($p->balance, 0) }}</td>

                            <td>
                                <span class="badge bg-{{
                                    $p->status == 'paid' ? 'success' :
                                    ($p->status == 'partial' ? 'warning' : 'secondary')
                                }}">
                                    {{ strtoupper($p->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $p->created_at->format('d M Y') }}<br>
                                <small class="text-muted">
                                    {{ $p->created_at->format('H:i') }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>

</div>