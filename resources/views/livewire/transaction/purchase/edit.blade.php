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
                        <h3 class="block-title">Edit Purchase</h3>
                    </div>

                    <div class="block-content">

                        {{-- Supplier --}}
                        <div class="mb-3" wire:ignore>
                            <label class="form-label">Supplier</label>
                            <select id="supplier-select" class="form-select">
                                <option></option>
                            </select>
                        </div>

                        {{-- Date --}}
                        <div class="mb-3">
                            <label class="form-label">Purchase Date</label>
                            <input type="date"
                                   class="form-control"
                                   wire:model="purchase_date">
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
                        <h3 class="block-title">Purchase Detail</h3>

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

                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Summary</h3>
                    </div>

                    <div class="block-content">

                        <div class="mb-2 d-flex justify-content-between">
                            <span>Subtotal</span>
                            <strong>
                                {{ number_format($this->subtotal, 2, '.', ',') }}
                            </strong>
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
                            <strong>
                                {{ number_format($this->grandTotal, 2, '.', ',') }}
                            </strong>
                        </div>

                        <div class="mt-4">
                            <button class="btn btn-success w-100">
                                Update Purchase
                            </button>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

{{-- =========================
     SELECT2 SCRIPT
==========================--}}
@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    initProducts();

    Livewire.on('reinit-select2', () => {
        setTimeout(initProducts, 50);
    });

    // SUPPLIER
    $('#supplier-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        minimumInputLength: 1,
        ajax: {
            url: '{{ route("api.suppliers.purchase") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => data
        }
    }).on('select2:select', function(e){

        Livewire.find(
            document.querySelector('[wire\\:id]').getAttribute('wire:id')
        ).set('supplier_id', e.params.data.id);

    });

    setTimeout(function(){

        let supplierId = @this.get('supplier_id');

        if (supplierId) {

            $.ajax({
                url: '{{ route("api.suppliers.purchase") }}',
                data: { id: supplierId },
                success: function(data){

                    if (data.results && data.results.length > 0) {

                        let supplier = data.results[0];

                        let option = new Option(
                            supplier.text,
                            supplier.id,
                            true,
                            true
                        );

                        $('#supplier-select').append(option).trigger('change.select2');
                    }
                }
            });

            const component = Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            );

            let items = component.get('items');

            $('.select-product').each(function(){

                let el = $(this);
                let index = el.data('index');

                if (items[index] && items[index].product_id) {

                    $.ajax({
                        url: '{{ route("api.products.purchase") }}',
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
        }

    }, 300);

});


function initProducts() {

    document.querySelectorAll('.select-product').forEach(function(el){

        if ($(el).data('select2')) return;

        let index = el.getAttribute('data-index');

        $(el).select2({
            theme: 'bootstrap-5',
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route("api.products.purchase") }}',
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => data
            }
        });

        $(el).on('select2:select', function(e){

            Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            ).call('selectProduct', {
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