<div class="container-fluid mt-4">

    <div class="row">

        {{-- LEFT SIDE --}}
        <div class="col-lg-8">

            {{-- HEADER --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        Payment - {{ $sale->sale_number }}
                    </h3>
                </div>

                <div class="block-content">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Patient:</strong><br>
                            {{ $sale->patient->name ?? '-' }}
                        </div>

                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{
                                $sale->status == 'paid' ? 'success' :
                                ($sale->status == 'partial' ? 'warning' : 'secondary')
                            }}">
                                {{ ucfirst($sale->status) }}
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
                                        wire:model="payment_method">
                                    <option value="">-- Select --</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="debit">Debit</option>
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
                            <label class="form-label">Notes</label>
                            <input type="text"
                                   class="form-control"
                                   wire:model="notes">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Upload Proof</label>
                            <input type="file"
                                   class="form-control"
                                   wire:model="payment_proof">
                        </div>

                        @error('payment_proof')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror

                        <button class="btn btn-success">
                            Save Payment
                        </button>

                    </form>

                </div>
            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="col-lg-4">

            {{-- SUMMARY --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Summary</h3>
                </div>

                <div class="block-content">

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Grand Total</span>
                        <strong>{{ number_format($sale->grand_total, 2) }}</strong>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Paid</span>
                        <strong>{{ number_format($paidTotal, 2) }}</strong>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Balance</span>
                        <strong class="{{ $balanceFinal > 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($balanceFinal, 2) }}
                        </strong>
                    </div>

                </div>
            </div>

            {{-- PAYMENT HISTORY --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Payment History</h3>
                </div>

                <div class="block-content">

                    @forelse($sale->payments as $payment)
                        <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-start"
                             wire:key="payment-history-{{ $payment->id }}">

                            {{-- LEFT CONTENT --}}
                            <div>
                                <div class="fw-semibold">
                                    {{ number_format($payment->amount, 2) }}
                                </div>

                                <small class="text-muted d-block">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}
                                    • {{ ucfirst($payment->payment_method) }}
                                </small>

                                @if($payment->notes)
                                    <small class="text-muted">
                                        {{ $payment->notes }}
                                    </small>
                                @endif
                            </div>

                            {{-- DELETE BUTTON --}}
                            <div>
                                <button class="btn btn-sm btn-alt-danger"
                                        wire:click="deletePayment({{ $payment->id }})"
                                        wire:loading.attr="disabled">
                                    <i class="fa fa-trash"></i>
                                </button>

                                @if($payment->payment_proof)
                                    <div class="mt-2">
                                        <a href="#"
                                            class="text-info fs-5"
                                            wire:click.prevent="previewProof('{{ $payment->payment_proof }}')"
                                            title="View Payment Proof">
                                                <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>

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

    <div wire:ignore.self class="modal fade" id="proofModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Payment Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">

                    @if($previewProofPath)

                        @php
                            $extension = pathinfo($previewProofPath, PATHINFO_EXTENSION);
                        @endphp

                        {{-- IMAGE --}}
                        @if(in_array(strtolower($extension), ['jpg','jpeg','png']))
                            <img src="{{ asset('storage/' . $previewProofPath) }}"
                                 class="img-fluid rounded shadow">
                        @endif

                        {{-- PDF --}}
                        @if(strtolower($extension) === 'pdf')
                            <iframe src="{{ asset('storage/' . $previewProofPath) }}"
                                    width="100%"
                                    height="500px"></iframe>
                        @endif

                    @endif

                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>

document.addEventListener('livewire:init', () => {

    Livewire.on('open-proof-modal', () => {
        let modal = new bootstrap.Modal(
            document.getElementById('proofModal')
        );
        modal.show();
    });

});

</script>
@endpush