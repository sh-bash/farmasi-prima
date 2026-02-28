<div>
    @role('patient')
        <div class="container mt-4">

            <div class="row mb-4">

                {{-- Sisa Hutang --}}
                <div class="col-md-6 mb-3">
                    <a href="{{ route('report.receivable.detail', auth()->user()->patient->id) }}"
                    class="text-decoration-none">

                        <div class="card shadow-sm border-0 hover-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Sisa Hutang</small>
                                        <h3 class="text-danger mb-0">
                                            Rp {{ number_format($patientOutstanding ?? 0) }}
                                        </h3>
                                    </div>
                                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                                        <i class="bi bi-cash-stack text-danger fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </a>
                </div>


                {{-- Total Order --}}
                <div class="col-md-6 mb-3">
                    <a href="{{ route('transaction.sales.index') }}"
                    class="text-decoration-none">

                        <div class="card shadow-sm border-0 hover-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">Total Order</small>
                                        <h3 class="text-primary mb-0">
                                            {{ $patientTotalOrders ?? 0 }}
                                        </h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                        <i class="bi bi-bag-check text-primary fs-4"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </a>
                </div>

            </div>

            {{-- Action Button Cards --}}
            <div class="row">

                {{-- Create Sales --}}
                <div class="col-md-6 mb-3">
                    <a href="{{ route('transaction.sales.create') }}" class="text-decoration-none">
                        <div class="card shadow-sm border-0 h-100 hover-card">
                            <div class="card-body text-center">
                                <i class="bi bi-plus-circle fs-1 text-success mb-3"></i>
                                <h5>Create Order</h5>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

        </div>

    @endrole

    @role('admin|apoteker')
        <div class="row">
            <div class="col-md-3">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="fs-2 fw-bold">{{ $pendingOrders }}</div>
                        <div class="text-muted">Pending Orders</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="fs-2 fw-bold">{{ $newPatients }}</div>
                        <div class="text-muted">New Patients</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="fs-2 fw-bold">{{ $totalOrders }}</div>
                        <div class="text-muted">Total Orders</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="block block-rounded text-center">
                    <div class="block-content block-content-full">
                        <div class="fs-2 fw-bold text-danger">
                            {{ $outOfStockProducts }}
                        </div>
                        <div class="text-muted">Stock 0 Products</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">

            {{-- LEFT BIG CHART --}}
            <div class="col-lg-8">
                <div class="block block-rounded">
                    <div class="block-header">
                        <h3 class="block-title">Earnings Summary</h3>
                    </div>
                    <div class="block-content">
                        <canvas id="earningsChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            {{-- RIGHT SMALL CARDS --}}
            <div class="col-lg-4">

                {{-- TOTAL SALES --}}
                <div class="block block-rounded">
                    <div class="block-content block-content-full d-flex justify-content-between align-items-center">

                        <div>
                            <div class="fs-3 fw-bold">
                                {{ number_format($totalSales,0) }}
                            </div>
                            <div class="text-muted">Total Sales</div>
                        </div>

                        <div style="width:120px; height:60px;">
                            <canvas id="salesMiniChart"></canvas>
                        </div>

                    </div>
                </div>

                {{-- TOTAL PURCHASES --}}
                <div class="block block-rounded mt-3">
                    <div class="block-content block-content-full d-flex justify-content-between align-items-center">

                        <div>
                            <div class="fs-3 fw-bold">
                                {{ number_format($totalPurchases,0) }}
                            </div>
                            <div class="text-muted">Total Purchases</div>
                        </div>

                        <div style="width:120px; height:60px;">
                            <canvas id="purchasesMiniChart"></canvas>
                        </div>

                    </div>
                </div>

                {{-- RECEIVABLE --}}
                <div class="block block-rounded mt-3">
                    <div class="block-content block-content-full d-flex justify-content-between align-items-center">

                        <div>
                            <div class="fs-3 fw-bold text-danger">
                                {{ number_format($totalReceivable,0) }}
                            </div>
                            <div class="text-muted">Receivable</div>
                        </div>

                        <div style="width:120px; height:60px;">
                            <canvas id="receivableMiniChart"></canvas>
                        </div>

                    </div>
                </div>

                {{-- PAYABLE --}}
                <div class="block block-rounded mt-3">
                    <div class="block-content block-content-full d-flex justify-content-between align-items-center">

                        <div>
                            <div class="fs-3 fw-bold text-warning">
                                {{ number_format($totalPayable,0) }}
                            </div>
                            <div class="text-muted">Payable</div>
                        </div>

                        <div style="width:120px; height:60px;">
                            <canvas id="payableMiniChart"></canvas>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    @endrole
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('earningsChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan','Feb','Mar','Apr','May','Jun'],
            datasets: [{
                label: 'Sales',
                data: [12,19,10,15,22,18],
                backgroundColor: '#3b82f6'
            },{
                label: 'Purchases',
                data: [8,11,14,9,17,13],
                backgroundColor: '#cbd5e1'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } }
        }
    });

    function miniChart(id, data, color) {
        const ctx = document.getElementById(id);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map((_, i) => i),
                datasets: [{
                    data: data,
                    fill: true,
                    backgroundColor: color + '33',
                    borderColor: color,
                    borderWidth: 2,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    miniChart('salesMiniChart', [10,15,12,18,20,22], '#3b82f6');
    miniChart('purchasesMiniChart', [8,12,14,9,16,18], '#6b7280');
    miniChart('receivableMiniChart', [5,6,4,7,6,5], '#dc2626');
    miniChart('payableMiniChart', [3,4,5,3,4,6], '#f59e0b');

});
</script>
@endpush
