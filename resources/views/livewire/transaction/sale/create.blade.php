<div class="container-fluid mt-4">

    <form wire:submit.prevent="save">

        <div class="row">

            {{-- =========================
                 LEFT SIDE (FORM)
            ==========================--}}
            <div class="col-lg-8">

                {{-- HEADER --}}
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Create Sale</h3>
                    </div>

                    <div class="block-content">

                        {{-- Patient --}}
                        <div class="mb-3" wire:ignore>
                            <label class="form-label">Patient</label>
                            <select id="patient-select" class="form-select"></select>
                        </div>

                        {{-- Date --}}
                        <div class="mb-3">
                            <label class="form-label">Sale Date</label>
                            <input type="date"
                                   class="form-control"
                                   wire:model="sale_date">
                        </div>

                        {{-- Notes --}}
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control"
                                      wire:model="notes"></textarea>
                        </div>

                    </div>
                </div>

                {{-- DETAIL --}}
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Sale Detail</h3>

                        <div class="block-options">
                            <button type="button"
                                    class="btn btn-sm btn-primary"
                                    wire:click="addRow">
                                + Add Row
                            </button>
                        </div>
                    </div>

                    <div class="block-content">

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="280">Product</th>
                                        <th width="120">Qty</th>
                                        <th width="150">Price</th>
                                        <th width="150">Discount</th>
                                        <th width="150">Total</th>
                                        <th width="80"></th>
                                    </tr>
                                </thead>
                                <tbody>

                                @foreach($items as $index => $item)
                                <tr wire:key="row-{{ $index }}">
                                    <td colspan="6" class="p-3">

                                        {{-- PRODUCT --}}
                                        <div class="mb-3" wire:ignore>
                                            <select class="form-select select-product"
                                                    data-index="{{ $index }}">
                                                <option></option>
                                            </select>
                                        </div>

                                        {{-- DETAIL --}}
                                        <div class="row g-3 align-items-end">

                                            <div class="col-md-2">

                                                @php
                                                    $stock = $stockAvailable[$index] ?? 0;
                                                    $badgeClass = $stock <= 0
                                                        ? 'bg-danger'
                                                        : ($stock <= 5 ? 'bg-warning text-dark' : 'bg-success');
                                                @endphp

                                                <label class="form-label">
                                                    Qty <span class="badge {{ $badgeClass }}">
                                                        Stock: {{ number_format($stock) }}
                                                    </span>
                                                </label>

                                                <input type="number"
                                                        class="form-control"
                                                        wire:model="items.{{ $index }}.qty"
                                                        wire:change="recalculateRow({{ $index }})"
                                                        min="1"
                                                        {{ $stock <= 0 ? 'disabled' : '' }}>

                                                <div class="mt-2">

                                                </div>

                                            </div>

                                            <div class="col-md-3">
                                                <label>Price</label>
                                                <input type="number"
                                                       class="form-control"
                                                       wire:model="items.{{ $index }}.price"
                                                       wire:change="recalculateRow({{ $index }})"
                                                       step="0.01">
                                            </div>

                                            <div class="col-md-3">
                                                <label>Discount</label>
                                                <input type="number"
                                                       class="form-control"
                                                       wire:model="items.{{ $index }}.discount"
                                                       wire:change="recalculateRow({{ $index }})"
                                                       step="0.01">
                                            </div>

                                            <div class="col-md-3">
                                                <label>Total</label>
                                                <div class="form-control bg-light text-end">
                                                    {{ number_format($item['total'], 2) }}
                                                </div>
                                            </div>

                                            <div class="col-md-1 text-end">
                                                <button type="button"
                                                        class="btn btn-danger"
                                                        wire:click="removeRow({{ $index }})">
                                                    X
                                                </button>
                                            </div>

                                        </div>

                                    </td>
                                </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

            {{-- =========================
                 RIGHT SIDE
            ==========================--}}
            <div class="col-lg-4">

                {{-- PAYMENT --}}
                <div class="block block-rounded mb-3">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Payment</h3>

                        <div class="block-options">
                            <button type="button"
                                    class="btn btn-sm btn-primary"
                                    wire:click="addPaymentRow">
                                + Add Payment
                            </button>
                        </div>
                    </div>

                    <div class="block-content">

                        @foreach($payments as $index => $payment)
                        <div class="border rounded p-3 mb-3"
                             wire:key="payment-{{ $index }}">

                            <div class="mb-2">
                                <label>Payment Date</label>
                                <input type="date"
                                       class="form-control"
                                       wire:model="payments.{{ $index }}.payment_date"
                                >
                            </div>

                            <div class="mb-2">
                                <label>Method</label>
                                <select class="form-select"
                                        wire:model.live="payments.{{ $index }}.method">
                                    <option value="">-- Select --</option>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="debit">Debit</option>
                                    <option value="bpjs">BPJS</option>
                                </select>
                            </div>

                            @if($payments[$index]['method'] !== 'bpjs')
                                <div class="mb-2">
                                    <label>Amount</label>
                                    <input type="number"
                                        class="form-control"
                                        wire:model="payments.{{ $index }}.amount"
                                        wire:change="recalculateRow({{ $index }})">
                                </div>

                                <div class="mb-2">
                                    <label>Upload Proof</label>
                                    <input type="file"
                                           class="form-control"
                                           wire:model="payments.{{ $index }}.payment_proof">
                                </div>

                                @error('payments.'.$index.'.payment_proof')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            @endif

                            <div class="text-end">
                                <button type="button"
                                        class="btn btn-danger btn-sm"
                                        wire:click="removePaymentRow({{ $index }})">
                                    Remove
                                </button>
                            </div>

                        </div>
                        @endforeach

                    </div>
                </div>

                {{-- SUMMARY --}}
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Summary</h3>
                    </div>

                    <div class="block-content">

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>{{ number_format($this->subtotal,2) }}</strong>
                        </div>

                        <div class="mb-2">
                            <label>Discount</label>
                            <input type="number"
                                   class="form-control"
                                   wire:model="discount">
                        </div>

                        <div class="mb-2">
                            <label>Tax</label>
                            <input type="number"
                                   class="form-control"
                                   wire:model="tax">
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fs-5">
                            <strong>Grand Total</strong>
                            <strong>{{ number_format($this->grandTotal,2) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Total Payment</span>
                            <strong>{{ number_format($this->totalPayment,2) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span>Balance</span>
                            <strong>{{ number_format($this->balance,2) }}</strong>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-success w-100">
                                Save Sale
                            </button>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

@push('scripts')
<script>

document.addEventListener('livewire:init', function () {

    initPatient();
    initProducts();

    Livewire.on('reinit-select2', () => {
        setTimeout(initProducts, 50);
    });

});

/* ===============================
   PATIENT SELECT2
================================*/
function initPatient(){

    if ($('#patient-select').data('select2')) return;

    $('#patient-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        minimumInputLength: 1,
        ajax: {
            url: '{{ route("api.patients.sale") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => data
        }
    }).on('select2:select', function(e){
        @this.set('patient_id', e.params.data.id);
    });
}

/* ===============================
   PRODUCT SELECT2
================================*/
function initProducts(){

    document.querySelectorAll('.select-product').forEach(function(el){

        if ($(el).data('select2')) return;

        let index = el.getAttribute('data-index');

        $(el).select2({
            theme: 'bootstrap-5',
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route("api.products.sale") }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => data
            }
        });

        $(el).on('select2:select', function(e){

            @this.call('selectProduct', {
                index: index,
                text: e.params.data.text,
                id: e.params.data.id,
                price: e.params.data.price
            });

        });

    });

}

</script>
@endpush