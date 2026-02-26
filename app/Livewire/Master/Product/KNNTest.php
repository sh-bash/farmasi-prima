<?php

namespace App\Livewire\Master\Product;

use Livewire\Component;
use App\Models\Master\Product;
use App\Helpers\KnnHelper;

class KnnTest extends Component
{
    public $products;
    public $selectedProductId = null;
    public $selectedProduct = null;
    public $substitutes = [];
    public $knnDetail = [];
    public $knnProcess = [];

    public function mount()
    {
        $this->products = Product::orderBy('name')->get();
    }

    protected $listeners = ['setProduct'];

    public function setProduct($productId)
    {
        $this->selectedProductId = $productId;

        $this->selectedProduct = Product::with('ingredients')
            ->find($productId);

        $this->substitutes = KnnHelper::findSubstitutes($productId, 3);

        $this->knnProcess = KNNHelper::findSubstitutesFullProcess($productId, 3);
        // $this->knnDetail = KNNHelper::findSubstitutesWithDetail($productId, 5);
        // $this->substitutes = collect($this->knnDetail['results'])->pluck('product');
    }

    public function render()
    {
        return view('livewire.master.product.k-n-n-test')->layout('layouts.app', [
            'title' => 'AI Test',
            'subtitle' => 'KNN Test',
        ]);
    }
}