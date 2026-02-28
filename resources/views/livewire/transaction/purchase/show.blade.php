<div class="container-fluid mt-4">

    <div class="row">

        {{-- LEFT SIDE --}}
        <div class="col-lg-8">

            {{-- HEADER --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">
                        Purchase #{{ $purchase->purchase_number }}
                    </h3>
                </div>

                <div class="block-content">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Supplier:</strong><br>
                            {{ $purchase->supplier->name }}
                        </div>

                        <div class="col-md-6">
                            <strong>Date:</strong><br>
                            {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Notes:</strong><br>
                        {{ $purchase->notes ?? '-' }}
                    </div>

                </div>
            </div>

            {{-- DETAIL --}}
            <div class="block block-rounded">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Purchase Detail</h3>
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
                                @foreach($purchase->details as $detail)
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
                            {{ number_format($purchase->discount, 2) }}
                        </strong>
                    </div>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Tax</span>
                        <strong>
                            {{ number_format($purchase->tax, 2) }}
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

        </div>

    </div>

</div>
