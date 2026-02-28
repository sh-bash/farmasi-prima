<?php

namespace App\Exports\Report;

use App\Models\Transaction\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SaleReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        protected $date_from,
        protected $date_to,
        protected $patient_id,
        protected $status,
        protected $products
    ) {}

    public function collection()
    {
        return Sale::query()
            ->with([
                'patient',
                'details' => function ($q) {
                    if (!empty($this->products)) {
                        $q->whereIn('product_id', $this->products);
                    }
                },
                'details.product'
            ])
            ->when($this->date_from, fn($q) =>
                $q->whereDate('sale_date', '>=', $this->date_from)
            )
            ->when($this->date_to, fn($q) =>
                $q->whereDate('sale_date', '<=', $this->date_to)
            )
            ->when($this->patient_id, fn($q) =>
                $q->where('patient_id', $this->patient_id)
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
            ->flatMap(function ($sale) {
                return $sale->details->map(function ($detail) use ($sale) {
                    return [
                        'date'     => $sale->sale_date,
                        'invoice'  => $sale->sale_number,
                        'patient'  => $sale->patient->name ?? '',
                        'status'   => ucfirst($sale->status),
                        'product'  => $detail->product->name ?? '',
                        'qty'      => $detail->qty,
                        'price'    => $detail->price,
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
            'Patient',
            'Status',
            'Product',
            'Qty',
            'Price',
            'Total',
        ];
    }
}