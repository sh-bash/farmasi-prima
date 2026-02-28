<?php

namespace App\Livewire\Report\Purchase;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction\Purchase;
use App\Models\Master\Supplier;
use App\Models\Master\Product;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Report\PurchaseReportExport;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // FILTER
    public $date_from;
    public $date_to;
    public $supplier_id;
    public $status;
    public $products = [];

    // Dropdown Data
    public $suppliers = [];
    public $allProducts = [];
    public $filterApplied = true;
    public $isFilterOpen = false;

    public function mount()
    {
        $this->date_from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->date_to   = Carbon::now()->endOfMonth()->format('Y-m-d');

        $this->suppliers = Supplier::orderBy('name')->get();
        $this->allProducts = Product::orderBy('name')->get();
    }

    public function updating()
    {
        $this->resetPage();
    }

    public function getDataProperty()
    {
        $query = Purchase::query()
                        ->with([
                            'supplier',
                            'details' => function ($q) {
                                if (!empty($this->products)) {
                                    $q->whereIn('product_id', $this->products);
                                }
                            },
                            'details.product'
                        ])
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
                        });

        // Jika belum klik filter → paksa kosong tapi tetap paginator
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
            new PurchaseReportExport(
                $this->date_from,
                $this->date_to,
                $this->supplier_id,
                $this->status,
                $this->products
            ),
            date('Y-m-d') . ' Purchase Report.xlsx'
        );
    }

    public function render()
    {
        return view('livewire.report.purchase.index', [
            'data' => $this->data
        ])->layout('layouts.app', [
            'title' => 'Purchase Report',
            'subtitle' => 'Generate and export purchase reports based on various filters.',
        ]);;
    }
}