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
    public $payments = [];

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

    // Payment Detail
    public function addPaymentRow()
    {
        $this->payments[] = [
            'payment_date' => now()->format('Y-m-d'),
            'amount' => 0,
            'method' => '',
            'reference' => '',
        ];
    }

    public function removePaymentRow($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
    }

    public function getTotalPaymentProperty()
    {
        return collect($this->payments)->sum('amount');
    }

    public function getBalanceProperty()
    {
        return $this->grandTotal - $this->totalPayment;
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

        foreach ($this->payments as $payment) {

            if ($payment['amount'] <= 0) {
                $this->addError('payments', 'Payment amount must be greater than zero.');
                return;
            }
        }

        if ($this->totalPayment > $this->grandTotal) {
            $this->addError('payments', 'Total payment cannot exceed grand total.');
            return;
        }

        DB::transaction(function () {
            $paidTotal = $this->totalPayment;
            $balance   = $this->grandTotal - $paidTotal;

            $status = 'posted';

            if ($paidTotal > 0) {
                $status = $balance == 0 ? 'paid' : 'partial';
            }

            $purchase = Purchase::create([
                'purchase_number' => 'PO-' . now()->format('YmdHis'),
                'purchase_date' => $this->purchase_date,
                'supplier_id' => $this->supplier_id,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax,
                'grand_total' => $this->grandTotal,
                'paid_total'      => $paidTotal,
                'balance'         => $balance,
                'status'          => $status,
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

            foreach ($this->payments as $payment) {

                if ($payment['amount'] > 0) {

                    $purchase->payments()->create([
                        'payment_date' => $payment['payment_date'],
                        'amount'       => $payment['amount'],
                        'method'       => $payment['method'],
                        'reference'    => $payment['reference'],
                    ]);
                }
            }
        });

        $this->dispatch('swal',
            icon: 'success',
            title: 'Success',
            text: 'Purchase created successfully'
        );

        return redirect()->route('transaction.sales.index');
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
