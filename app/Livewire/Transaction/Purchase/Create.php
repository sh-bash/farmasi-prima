<?php

namespace App\Livewire\Transaction\Purchase;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction\Purchase;

class Create extends Component
{
    public $supplier_id;
    public $purchase_date;
    public $notes;

    public $discount = 0;
    public $tax = 0;

    public $items = [];

    protected $listeners = [
        'selectProduct',
        'setSupplier'
    ];

    /* =========================================
       MOUNT
    ==========================================*/

    public function mount()
    {
        $this->purchase_date = now()->format('Y-m-d');
    }

    /* =========================================
       SUPPLIER
    ==========================================*/

    public function setSupplier($data)
    {
        $this->supplier_id = $data['id'];
    }

    /* =========================================
       DETAIL ROW
    ==========================================*/

    public function addRow()
    {
        $this->items[] = [
            'product_id' => null,
            'qty' => 1,
            'price' => 0,
            'discount' => 0,
            'total' => 0,
        ];

        $this->dispatch('reinit-select2');
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /* =========================================
       PRODUCT SELECT (FROM SELECT2)
    ==========================================*/

    public function selectProduct($data)
    {
        $index = $data['index'];

        $this->items[$index]['product_id'] = $data['id'];
        $this->items[$index]['product_name'] = $data['text'];
        $this->items[$index]['price'] = $data['price'] ?? 0;

        $this->recalculateRow($index);
    }

    /* =========================================
       AUTO CALCULATION
    ==========================================*/

    public function updatedItems($value, $key)
    {
        // key format: items.0.qty
        $parts = explode('.', $key);

        if (count($parts) >= 3) {
            $index = $parts[1];
            $this->recalculateRow($index);
        }
    }

    public function recalculateRow($index)
    {
        $qty = floatval($this->items[$index]['qty'] ?? 0);
        $price = floatval($this->items[$index]['price'] ?? 0);
        $discount = floatval($this->items[$index]['discount'] ?? 0);

        $total = ($qty * $price) - $discount;

        $this->items[$index]['total'] = max($total, 0);
    }

    public function getSubtotalProperty()
    {
        return collect($this->items)->sum('total');
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal - floatval($this->discount) + floatval($this->tax);
    }

    /* =========================================
       SAVE
    ==========================================*/

    public function save()
    {
        $this->validate([
            'supplier_id' => 'required',
            'purchase_date' => 'required|date',
            'items.*.product_id' => 'required',
            'items.*.qty' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () {

            $purchase = Purchase::create([
                'purchase_number' => 'PO-' . now()->format('YmdHis'),
                'purchase_date' => $this->purchase_date,
                'supplier_id' => $this->supplier_id,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax,
                'grand_total' => $this->grandTotal,
                'paid_total' => 0,
                'balance' => $this->grandTotal,
                'status' => 'posted',
                'notes' => $this->notes,
            ]);

            foreach ($this->items as $item) {
                $purchase->details()->create([
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'total' => $item['total'],
                ]);
            }
        });

        $this->dispatch('swal',
            icon: 'success',
            title: 'Success',
            text: 'Purchase created successfully'
        );

        return redirect()->route('transaction.purchases');
    }

    /* =========================================
       RENDER
    ==========================================*/

    public function render()
    {
        return view('livewire.transaction.purchase.create')
            ->layout('layouts.app', [
                'title' => 'Create Purchase',
                'subtitle' => 'Add new purchase transaction',
            ]);
    }
}