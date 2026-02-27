<div class="container-fluid mt-4">

    <form wire:submit.prevent="save">

        <div class="row">

            {{-- =========================
                 LEFT SIDE (FORM)
            ==========================--}}
            <div class="col-lg-8">

                {{-- HEADER BLOCK --}}
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Edit Sale</h3>
                    </div>

                    <div class="block-content">

                        {{-- Patient --}}
                        <div class="mb-3" wire:ignore>
                            <label class="form-label">Patient</label>
                            <select id="patient-select" class="form-select">
                                <option></option>
                            </select>
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

                {{-- =========================
                     DETAIL BLOCK
                ==========================--}}
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
                                                <label class="form-label">Qty</label>
                                                <input type="number"
                                                       class="form-control"
                                                       wire:model="items.{{ $index }}.qty"
                                                       wire:change="recalculateRow({{ $index }})">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Price</label>
                                                <input type="number"
                                                       class="form-control"
                                                       wire:model="items.{{ $index }}.price"
                                                       wire:change="recalculateRow({{ $index }})">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Discount</label>
                                                <input type="number"
                                                       class="form-control"
                                                       wire:model="items.{{ $index }}.discount"
                                                       wire:change="recalculateRow({{ $index }})">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label">Total</label>
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
                 RIGHT SIDE (SUMMARY)
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

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Payment Date</label>
                                    <input type="date"
                                           class="form-control"
                                           wire:model.live="payments.{{ $index }}.payment_date">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Method</label>
                                    <select class="form-select"
                                            wire:model.live="payments.{{ $index }}.method">
                                        <option value="">-- Select --</option>
                                        <option value="cash">Cash</option>
                                        <option value="transfer">Transfer</option>
                                        <option value="debit">Debit</option>
                                        <option value="bpjs">BPJS</option>
                                    </select>
                                </div>
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

                            <div class="mb-3">
                                <label class="form-label">Reference</label>
                                <input type="text"
                                       class="form-control"
                                       wire:model.defer="payments.{{ $index }}.reference">
                            </div>

                            @if(!empty($payments[$index]['existing_proof']))
                                <div class="mb-2">
                                    <small>Current Proof:</small><br>
                                    <a href="#"
                                    wire:click.prevent="previewProof('{{ $payments[$index]['existing_proof'] }}')">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </div>
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

                        <div class="mb-2 d-flex justify-content-between">
                            <span>Subtotal</span>
                            <strong>{{ number_format($this->subtotal, 2, '.', ',') }}</strong>
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
                            <strong>{{ number_format($this->grandTotal, 2, '.', ',') }}</strong>
                        </div>

                        <div class="mb-2 d-flex justify-content-between">
                            <span>Total Payment</span>
                            <strong>{{ number_format($this->totalPayment, 2) }}</strong>
                        </div>

                        <div class="mb-2 d-flex justify-content-between">
                            <span>Balance</span>
                            <strong>{{ number_format($this->balance, 2) }}</strong>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-success w-100">
                                Update Sale
                            </button>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </form>

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

document.addEventListener('livewire:init', function () {

    initPatient();
    initProducts();

    Livewire.on('reinit-select2', () => {
        setTimeout(initProducts, 50);
    });

    preloadData();

    Livewire.on('open-proof-modal', () => {
        let modal = new bootstrap.Modal(
            document.getElementById('proofModal')
        );
        modal.show();
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

        let component = Livewire.find(
            document.querySelector('[wire\\:id]').getAttribute('wire:id')
        );

        component.set('patient_id', e.params.data.id);
    });
}


/* ===============================
   PRELOAD EXISTING DATA (EDIT)
================================*/
function preloadData(){

    setTimeout(function(){

        let component = Livewire.find(
            document.querySelector('[wire\\:id]').getAttribute('wire:id')
        );

        let patientId = component.get('patient_id');

        /* --------- PRELOAD PATIENT --------- */
        if (patientId) {

            $.ajax({
                url: '{{ route("api.patients.sale") }}',
                data: { id: patientId },
                success: function(data){

                    if (data.results && data.results.length > 0) {

                        let patient = data.results[0];

                        let option = new Option(
                            patient.text,
                            patient.id,
                            true,
                            true
                        );

                        $('#patient-select')
                            .append(option)
                            .trigger('change.select2');
                    }
                }
            });
        }

        /* --------- PRELOAD PRODUCTS --------- */
        let items = component.get('items');

        $('.select-product').each(function(){

            let el = $(this);
            let index = el.data('index');

            if (items[index] && items[index].product_id) {

                $.ajax({
                    url: '{{ route("api.products.sale") }}',
                    data: { id: items[index].product_id },
                    success: function(data){

                        if (data.results && data.results.length > 0) {

                            let product = data.results[0];

                            let option = new Option(
                                product.text,
                                product.id,
                                true,
                                true
                            );

                            el.append(option).trigger('change');
                        }
                    }
                });
            }

        });

    }, 300);
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

            let component = Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            );

            component.call('selectProduct', {
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