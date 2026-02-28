<div class="container-fluid mt-4">

    <div class="row">

        {{-- LEFT SIDE --}}
        <div class="col-lg-8">

            {{-- HEADER --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        Sale #{{ $sale->sale_number }}
                    </h3>
                </div>

                <div class="block-content">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Patient:</strong><br>
                            {{ $sale->patient->name ?? '-' }}
                        </div>

                        <div class="col-md-6">
                            <strong>Date:</strong><br>
                            {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Notes:</strong><br>
                        {{ $sale->notes ?? '-' }}
                    </div>

                </div>
            </div>

            {{-- DETAIL --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Sale Detail</h3>
                </div>

                <div class="block-content">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="120">Qty</th>
                                    <th width="150">Price</th>
                                    <th width="150">Discount</th>
                                    <th width="150">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->details as $detail)
                                    <tr>
                                        <td>
                                            {{ $detail->product->name }}
                                        </td>
                                        <td>{{ $detail->qty }}</td>
                                        <td>{{ number_format($detail->price, 2) }}</td>
                                        <td>{{ number_format($detail->discount, 2) }}</td>
                                        <td>
                                            <strong>
                                                {{ number_format($detail->total, 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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
                        <span>Subtotal</span>
                        <strong>
                            {{ number_format($this->subtotal, 2) }}
                        </strong>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Discount</span>
                        <strong>
                            {{ number_format($sale->discount, 2) }}
                        </strong>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Tax</span>
                        <strong>
                            {{ number_format($sale->tax, 2) }}
                        </strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fs-5">
                        <strong>Grand Total</strong>
                        <strong>
                            {{ number_format($this->grandTotal, 2) }}
                        </strong>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('transaction.sales.index') }}"
                           class="btn btn-secondary w-100">
                            Back
                        </a>
                    </div>

                </div>
            </div>

            {{-- PAYMENT HISTORY --}}
            <div class="block block-rounded mt-3">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Payment History</h3>
                </div>

                <div class="block-content">

                    @forelse($sale->payments as $payment)

                        <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-start">

                            {{-- LEFT --}}
                            <div>

                                <div class="fw-semibold">
                                    {{ number_format($payment->amount, 2) }}
                                </div>

                                <small class="text-muted d-block">
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}
                                    • {{ strtoupper($payment->payment_method) }}
                                </small>

                                @if($payment->notes)
                                    <small class="text-muted">
                                        {{ $payment->notes }}
                                    </small>
                                @endif

                            </div>

                            {{-- RIGHT --}}
                            <div class="text-end">

                                {{-- Proof --}}
                                @if($payment->payment_proof)
                                    <a href="#"
                                    wire:click.prevent="previewProof('{{ $payment->payment_proof }}')"
                                    class="text-info fs-5"
                                    title="View Proof">
                                        <i class="fa fa-eye"></i>
                                    </a>
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
                            $ext = pathinfo($previewProofPath, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                            <img src="{{ asset('storage/'.$previewProofPath) }}"
                                 class="img-fluid rounded shadow">
                        @endif

                        @if(strtolower($ext) === 'pdf')
                            <iframe src="{{ asset('storage/'.$previewProofPath) }}"
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
