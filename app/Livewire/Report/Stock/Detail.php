<?php

namespace App\Livewire\Report\Stock;

use App\Models\Master\Product;
use App\Helpers\StockHelper;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Detail extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $productId;
    public $product;
    public $currentStock;

    public function mount($productId)
    {
        $this->productId = $productId;
        $this->product = Product::findOrFail($productId);

        // Ambil stock terakhir via helper
        $this->currentStock = StockHelper::getCurrentStock($productId);
    }

    public function getDataProperty()
    {
        $purchase = DB::table('purchase_details as pd')
            ->join('purchases as p', 'p.id', '=', 'pd.purchase_id')
            ->where('pd.product_id', $this->productId)
            ->select(
                'p.purchase_date as date',
                DB::raw("'Purchase' as type"),
                'p.purchase_number as reference',
                'pd.qty as stock_in',
                DB::raw('0 as stock_out')
            );

        $sale = DB::table('sale_details as sd')
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->where('sd.product_id', $this->productId)
            ->select(
                's.sale_date as date',
                DB::raw("'Sale' as type"),
                's.sale_number as reference',
                DB::raw('0 as stock_in'),
                'sd.qty as stock_out'
            );

        $union = $purchase->unionAll($sale);

        // Hitung balance ASC dulu
        $running = DB::query()
            ->fromSub($union, 'stock_moves')
            ->select(
                '*',
                DB::raw('SUM(stock_in - stock_out) OVER (ORDER BY date, reference ASC) as balance')
            );

        // Baru tampilkan DESC
        return DB::query()
            ->fromSub($running, 'final_moves')
            ->orderBy('date', 'desc')
            ->orderBy('reference', 'desc')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.report.stock.detail', [
            'data' => $this->data
        ])->layout('layouts.app', [
            'title' => 'Stock Detail',
            'subtitle' => 'Stock movement history'
        ]);
    }
}