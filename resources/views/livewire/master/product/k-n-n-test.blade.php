<div class="block">
    <div class="block-header">
        <h3 class="block-title">KNN Substitution Test</h3>
    </div>

    <div class="block-content">

        {{-- Select2 --}}
        <div wire:ignore class="mb-3">
            <label>Pilih Produk</label>
            <select id="productSelect" class="form-control">
                <option value="">-- pilih produk --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Selected Product Info --}}
        @if($selectedProduct)
            <div class="mb-3">
                <h5>{{ $selectedProduct->name }}</h5>

                <div class="text-muted">
                    @foreach($selectedProduct->ingredients as $i)
                        <div>
                            {{ $i->name }}
                            {{ $i->pivot->strength }}{{ $i->pivot->unit }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Result tetap seperti sebelumnya --}}
        @if($selectedProductId)
            <hr>
            <h5>Hasil Substitusi</h5>

            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kandungan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($substitutes as $s)
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td class="text-muted">
                                @foreach($s->ingredients as $i)
                                    <div>
                                        {{ $i->name }}
                                        {{ $i->pivot->strength }}{{ $i->pivot->unit }}
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2">Tidak ada substitusi</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

    </div>
</div>

{{-- Select2 Script --}}
@push('scripts')
<script>
function initSelect2() {
    let el = $('#productSelect');

    if (el.hasClass("select2-hidden-accessible")) {
        el.select2('destroy');
    }

    el.select2({
        placeholder: 'Cari produk',
        width: '100%'
    });

    el.off('change').on('change', function () {
        let id = $(this).val();
        Livewire.dispatch('setProduct', { productId: id });
    });
}

// saat pertama load
document.addEventListener('DOMContentLoaded', function () {
    initSelect2();
});

// setiap Livewire update
document.addEventListener('livewire:navigated', initSelect2);
document.addEventListener('livewire:update', initSelect2);
</script>
@endpush