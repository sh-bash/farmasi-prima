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

        $this->substitutes = KnnHelper::findSubstitutes($productId, 5);
    }

    public function render()
    {
        return view('livewire.master.product.k-n-n-test');
    }
}