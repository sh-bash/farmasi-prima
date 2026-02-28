<div class="container mt-4">

    {{-- Button Add --}}
    <div class="mb-3">
        <a href="{{ route('transaction.sales.create') }}"
           class="btn btn-primary">
            + Add Sale
        </a>
    </div>

    {{-- Search --}}
    <div class="mb-2">
        <input type="text"
               class="form-control"
               placeholder="Search sale..."
               wire:model.live="search">
    </div>

    {{-- Table Block --}}
    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Sale List</h3>

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
                        <th>Patient</th>
                        <th>Grand Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th width="160">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $s)
                        <tr>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-alt-secondary"
                                            wire:click="toggleDetail({{ $s->id }})">
                                        @if($expanded === $s->id)
                                            <i class="fa fa-chevron-up"></i>
                                        @else
                                            <i class="fa fa-chevron-down"></i>
                                        @endif
                                    </button>

                                    <a href="{{ route('transaction.sales.show', $s->id) }}"
                                       class="btn btn-sm btn-alt-info">
                                        <i class="fa fa-eye"></i>
                                    </a>


                                        @if($s->status !== 'paid')
                                            @role('admin')
                                                <a href="{{ route('transaction.sales.edit', $s->id) }}"
                                                class="btn btn-sm btn-alt-warning"
                                                title="Edit">
                                                    <i class="fa fa-pencil-alt"></i>
                                                </a>
                                            @endrole

                                            <a href="{{ route('transaction.sales.payment', $s->id) }}"
                                            class="btn btn-sm btn-alt-info"
                                            title="Payment">
                                                <i class="fa fa-money-bill"></i>
                                            </a>
                                        @endif

                                    @role('admin')
                                        <button class="btn btn-sm btn-alt-danger"
                                                wire:click="confirmDelete({{ $s->id }})"
                                                title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endrole
                                </div>
                            </td>

                            <td>{{ $s->sale_number }}</td>

                            <td>{{ \Carbon\Carbon::parse($s->sale_date)->format('d M Y') }}</td>

                            <td>{{ $s->patient->name ?? '-' }}</td>

                            <td>{{ number_format($s->grand_total, 0) }}</td>
                            <td>{{ number_format($s->paid_total, 0) }}</td>
                            <td>{{ number_format($s->balance, 0) }}</td>

                            <td>
                                <span class="badge bg-{{
                                    $s->status == 'paid' ? 'success' :
                                    ($s->status == 'partial' ? 'warning' : 'secondary')
                                }}">
                                    {{ strtoupper($s->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $s->created_at->format('d M Y') }}<br>
                                <small class="text-muted">
                                    {{ $s->created_at->format('H:i') }}
                                </small>
                            </td>
                        </tr>

                        @if($expanded === $s->id)
                            <tr>
                                <td colspan="9" class="bg-light">

                                    <div class="p-3">

                                        {{-- PRODUCTS --}}
                                        <h6 class="fw-bold">Products</h6>

                                        <table class="table table-sm table-bordered mb-3">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th width="150">Qty</th>
                                                    <th width="150">Price</th>
                                                    <th width="150">Discount</th>
                                                    <th width="150">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($s->details as $detail)
                                                    <tr>
                                                        <td>{{ $detail->product->name }}</td>
                                                        <td>{{ $detail->qty }}</td>
                                                        <td>{{ number_format($detail->price, 2) }}</td>
                                                        <td>{{ number_format($detail->discount, 2) }}</td>
                                                        <td>{{ number_format($detail->total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        {{-- PAYMENTS --}}
                                        <h6 class="fw-bold">Payments</h6>

                                        @if($s->payments->count())
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th width="200">Date</th>
                                                        <th width="200">Amount</th>
                                                        <th width="200">Method</th>
                                                        <th>Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($s->payments as $payment)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                                            <td>{{ ucfirst($payment->payment_method) }}</td>
                                                            <td>{{ $payment->notes }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="text-muted">No payment yet.</div>
                                        @endif

                                    </div>

                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $sales->links() }}
            </div>
        </div>
    </div>

</div>