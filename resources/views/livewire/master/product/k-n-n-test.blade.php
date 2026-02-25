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
                <h5>{{ $selectedProduct->name }} - {{ $selectedProduct->form->name }}</h5>

                <div class="text-muted">
                    @foreach($selectedProduct->ingredients as $i)
                        <div>
                            {{ $i->name }}
                            {{ number_format($i->pivot->strength, 0) }} {{ $i->pivot->unit }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($knnProcess))
        <div class="block block-rounded block-bordered mt-4">
            <div class="block-header bg-primary text-white">
                <h3 class="block-title">AI KNN Calculation Process</h3>
            </div>

            <div class="block-content">

                {{-- STEP 1 --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>STEP 1 - Target Product</strong>
                    </div>
                    <div class="card-body">
                        <h5>{{ $knnProcess['step_1_target']['name'] }}</h5>

                        <ul class="mb-0">
                            @foreach($knnProcess['step_1_target']['ingredients'] as $ing)
                                <li>
                                    {{ $ing['name'] }} :
                                    <span class="badge bg-info">
                                        {{ $ing['strength'] }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Step 2 - Candidate --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>STEP 2 - Candidate Filtering</strong>
                    </div>
                    <div class="card-body">
                        <p>Total Kandidat:
                            <span class="badge bg-warning">
                                {{ count($knnProcess['step_2_candidates']) }}
                            </span>
                        </p>

                        <ul>
                            @foreach($knnProcess['step_2_candidates'] as $c)
                                <li>{{ $c }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Step 3 - Vector Space --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>STEP 3 - Vector Space (Fitur)</strong>
                    </div>
                    <div class="card-body">
                        @foreach($knnProcess['step_3_vector_space'] as $feature)
                            <span class="badge bg-secondary me-1">
                                {{ $feature }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Step 5 - Distance Calculation Detail --}}
                @foreach($knnProcess['step_5_distance_raw'] as $index => $result)
                    <div class="card mb-4 border">
                        <div class="card-header bg-dark text-white">
                            Candidate: {{ $result['product'] }}
                            <span class="float-end">
                                Distance:
                                <span class="badge bg-danger">
                                    {{ number_format($result['distance'], 4) }}
                                </span>
                            </span>
                        </div>

                        <div class="card-body p-0">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Feature</th>
                                        <th>Target</th>
                                        <th>Candidate</th>
                                        <th>Diff</th>
                                        <th>Square</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['calculation'] as $step)
                                    <tr @if(abs($step['diff']) > 50) class="table-danger" @endif>
                                        <td>{{ $step['feature'] }}</td>
                                        <td>{{ $step['target'] }}</td>
                                        <td>{{ $step['candidate'] }}</td>
                                        <td>{{ $step['diff'] }}</td>
                                        <td>{{ $step['square'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                {{-- Step 7 Final Ranking--}}
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        FINAL RESULT (Top K)
                    </div>
                    <div class="card-body">

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Product</th>
                                    <th>Distance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($knnProcess['step_7_final_result'] as $rank => $r)
                                <tr @if($rank == 0) class="table-success fw-bold" @endif>
                                    <td>#{{ $rank + 1 }}</td>
                                    <td>{{ $r['product'] }}</td>
                                    <td>{{ number_format($r['distance'], 4) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>

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
                        <th>Form</th>
                        <th>Kandungan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($substitutes as $s)
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td>{{ $s->form->name }}</td>
                            <td class="text-muted">
                                @foreach($s->ingredients as $i)
                                    <div>
                                        {{ $i->name }}
                                        {{ number_format($i->pivot->strength, 0) }} {{ $i->pivot->unit }}
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">Tidak ada substitusi</td>
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