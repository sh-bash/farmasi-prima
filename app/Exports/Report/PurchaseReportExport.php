<?php

namespace App\Exports\Report;

use App\Models\Transaction\Purchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected $date_from,
        protected $date_to,
        protected $supplier_id,
        protected $status,
        protected $products
    ) {}

    public function collection()
    {
        return Purchase::query()
            ->with(['supplier', 'details.product'])
            ->when($this->date_from, fn($q) =>
                $q->whereDate('purchase_date', '>=', $this->date_from)
            )
            ->when($this->date_to, fn($q) =>
                $q->whereDate('purchase_date', '<=', $this->date_to)
            )
            ->when($this->supplier_id, fn($q) =>
                $q->where('supplier_id', $this->supplier_id)
            )
            ->when($this->status, fn($q) =>
                $q->where('status', $this->status)
            )
            ->when(!empty($this->products), function ($q) {
                $q->whereHas('details', function ($detail) {
                    $detail->whereIn('product_id', $this->products);
                });
            })
            ->get()
            ->flatMap(function ($purchase) {
                return $purchase->details->map(function ($detail) use ($purchase) {
                    return [
                        'date'     => $purchase->purchase_date,
                        'invoice'  => $purchase->purchase_number,
                        'supplier' => $purchase->supplier->name ?? '',
                        'status'   => ucfirst($purchase->status),
                        'product'  => $detail->product->name ?? '',
                        'qty'      => $detail->qty,
                        'price'      => $detail->price,
                        'total'    => $detail->total,
                    ];
                });
            });
    }

    public function headings(): array
    {
        return [
            'Date',
            'Invoice',
            'Supplier',
            'Status',
            'Product',
            'Qty',
            'Price',
            'Total',
        ];
    }
}