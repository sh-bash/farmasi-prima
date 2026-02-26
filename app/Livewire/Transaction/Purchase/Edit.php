<?php

namespace App\Livewire\Transaction\Purchase;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction\Purchase;

class Edit extends Component
{
    public Purchase $purchase;

    public $supplier_id;
    public $purchase_date;
    public $notes;

    public $discount = 0;
    public $tax = 0;

    public $items = [];

    public function mount($id)
    {
        $this->purchase = Purchase::with('details.product')
            ->findOrFail($id);

        $this->supplier_id = $this->purchase->supplier_id;
        $this->purchase_date = $this->purchase->purchase_date;
        $this->notes = $this->purchase->notes;
        $this->discount = $this->purchase->discount;
        $this->tax = $this->purchase->tax;

        foreach ($this->purchase->details as $detail) {
            $this->items[] = [
                'product_id' => $detail->product_id,
                'product_name' => $detail->product->name,
                'qty' => $detail->qty,
                'price' => $detail->price,
                'discount' => $detail->discount,
                'total' => $detail->total,
            ];
        }
    }

    /* ===============================
       ROW MANAGEMENT
    ===============================*/

    public function addRow()
    {
        $this->items[] = [
            'product_id' => null,
            'product_name' => '',
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

    /* ===============================
       SELECT PRODUCT
    ===============================*/

    public function selectProduct($data)
    {
        $index = $data['index'];

        $this->items[$index]['product_id'] = $data['id'];
        $this->items[$index]['product_name'] = $data['text'];
        $this->items[$index]['price'] = (float) $data['price'];

        $this->recalculateRow($index);
    }

    /* ===============================
       RECALCULATION
    ===============================*/

    public function recalculateRow($index)
    {
        $qty = (float) ($this->items[$index]['qty'] ?? 0);
        $price = (float) ($this->items[$index]['price'] ?? 0);
        $discount = (float) ($this->items[$index]['discount'] ?? 0);

        $this->items[$index]['total'] = ($qty * $price) - $discount;
    }

    public function getSubtotalProperty()
    {
        return collect($this->items)->sum('total');
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal
            - (float) $this->discount
            + (float) $this->tax;
    }

    /* ===============================
       UPDATE
    ===============================*/

    public function save()
    {
        $this->validate([
            'supplier_id' => 'required',
            'purchase_date' => 'required',
            'items.*.product_id' => 'required',
        ]);

        DB::transaction(function () {

            $this->purchase->update([
                'supplier_id' => $this->supplier_id,
                'purchase_date' => $this->purchase_date,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax,
                'grand_total' => $this->grandTotal,
                'balance' => $this->grandTotal,
                'notes' => $this->notes,
            ]);

            // delete old detail
            $this->purchase->details()->delete();

            // insert ulang
            foreach ($this->items as $item) {
                $this->purchase->details()->create([
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'total' => $item['total'],
                ]);
            }
        });

        return redirect()->route('transaction.purchases');
    }

    public function render()
    {
        return view('livewire.transaction.purchase.edit')
            ->layout('layouts.app', [
                'title' => 'Edit Purchase',
                'subtitle' => 'Update purchase transaction',
            ]);
    }
}