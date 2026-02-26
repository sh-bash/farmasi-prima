<div class="container-fluid mt-4">

    <div class="row">

        {{-- LEFT SIDE --}}
        <div class="col-lg-8">

            {{-- HEADER --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        Payment - {{ $purchase->purchase_number }}
                    </h3>
                </div>

                <div class="block-content">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Supplier:</strong><br>
                            {{ $purchase->supplier->name }}
                        </div>

                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-info">
                                {{ ucfirst($purchase->status) }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- PAYMENT FORM --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Add Payment</h3>
                </div>

                <div class="block-content">

                    <form wire:submit.prevent="savePayment">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Payment Date</label>
                                <input type="date"
                                       class="form-control"
                                       wire:model="payment_date">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Method</label>
                                <select class="form-select"
                                        wire:model="method">
                                    <option value="">-- Select --</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="giro">Giro</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number"
                                   class="form-control"
                                   wire:model="amount">
                            @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reference</label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="reference">
                        </div>

                        <button class="btn btn-success">
                            Save Payment
                        </button>

                    </form>

                </div>
            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="col-lg-4">

            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Summary</h3>
                </div>

                <div class="block-content">

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Grand Total</span>
                        <strong>{{ number_format($purchase->grand_total, 2) }}</strong>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Paid</span>
                        <strong>{{ number_format($paidTotal, 2) }}</strong>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Balance</span>
                        <strong>{{ number_format($balanceFinal, 2) }}</strong>
                    </div>

                </div>
            </div>

            {{-- PAYMENT HISTORY --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Payment History</h3>
                </div>

                <div class="block-content">

                    @forelse($purchase->payments as $payment)
                        <div class="border rounded p-2 mb-2">
                            <strong>{{ number_format($payment->amount, 2) }}</strong><br>
                            <small>
                                {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}
                                - {{ ucfirst($payment->method) }}
                            </small><br>
                            <small>{{ $payment->reference }}</small>
                        </div>
                    @empty
                        <div class="text-muted">
                            No payment yet.
                        </div>
                    @endforelse

                </div>
            </div>

        </div>

    </div>

</div>