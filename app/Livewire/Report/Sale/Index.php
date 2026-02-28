<?php

namespace App\Livewire\Report\Sale;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction\Sale;
use App\Models\Master\Patient;
use App\Models\Master\Product;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Report\SaleReportExport;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // FILTER
    public $date_from;
    public $date_to;
    public $patient_id;
    public $status;
    public $products = [];

    // Dropdown
    public $patients = [];
    public $allProducts = [];

    public $filterApplied = true;
    public $isFilterOpen = false;

    public function mount()
    {
        $this->date_from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->date_to   = Carbon::now()->endOfMonth()->format('Y-m-d');

        $this->patients    = Patient::orderBy('name')->get();
        $this->allProducts = Product::orderBy('name')->get();
    }

    public function updating()
    {
        $this->resetPage();
    }

    public function getDataProperty()
    {
        $query = Sale::query()
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
            });

        if (!$this->filterApplied) {
            $query->whereRaw('1 = 0');
        }

        return $query->latest()->paginate(10);
    }

    public function toggleFilter()
    {
        $this->isFilterOpen = !$this->isFilterOpen;
    }

    public function applyFilter()
    {
        $this->filterApplied = true;
        $this->resetPage();
    }

    public function export()
    {
        return Excel::download(
            new SaleReportExport(
                $this->date_from,
                $this->date_to,
                $this->patient_id,
                $this->status,
                $this->products
            ),
            date('Y-m-d') . ' Sales Report.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.report.sale.index', [
            'data' => $this->data
        ])->layout('layouts.app', [
            'title' => 'Sale Report',
            'subtitle' => 'Generate and export sales reports based on patient and product filters.',
        ]);
    }
}