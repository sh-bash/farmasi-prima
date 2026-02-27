<?php

namespace App\Livewire\Transaction\Sale;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction\Sale;
use Livewire\WithFileUploads;


class Edit extends Component
{
    use WithFileUploads;
    public Sale $sale;

    public $patient_id;
    public $sale_date;
    public $notes;

    public $discount = 0;
    public $tax = 0;

    public $items = [];
    public $payments = [];
    public $previewProofPath = null;

    public function mount($id)
    {
        $this->sale = Sale::with('details.product', 'payments')
            ->findOrFail($id);

        $this->patient_id = $this->sale->patient_id;
        $this->sale_date = optional($this->sale->sale_date)
                    ? \Carbon\Carbon::parse($this->sale->sale_date)->format('Y-m-d')
                    : null;
        $this->notes      = $this->sale->notes;
        $this->discount   = $this->sale->discount;
        $this->tax        = $this->sale->tax;

        foreach ($this->sale->details as $detail) {
            $this->items[] = [
                'product_id'   => $detail->product_id,
                'product_name' => $detail->product->name,
                'qty'          => $detail->qty,
                'price'        => $detail->price,
                'discount'     => $detail->discount,
                'total'        => $detail->total,
            ];
        }

        foreach ($this->sale->payments as $payment) {
            $this->payments[] = [
                'payment_date' => optional($payment->payment_date)
                ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d')
                : null,
                'amount'       => $payment->amount,
                'method'       => $payment->payment_method,
                'reference'    => $payment->notes,
                'existing_proof'=> $payment->payment_proof,
            ];
        }

        if (empty($this->payments)) {
            $this->payments[] = [
                'payment_date' => now()->format('Y-m-d'),
                'amount'       => 0,
                'method'       => '',
                'reference'    => '',
            ];
        }
    }

    /* ===============================
       ROW MANAGEMENT
    ===============================*/

    public function addRow()
    {
        $this->items[] = [
            'product_id'   => null,
            'product_name' => '',
            'qty'          => 1,
            'price'        => 0,
            'discount'     => 0,
            'total'        => 0,
        ];

        $this->dispatch('reinit-select2');
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function addPaymentRow()
    {
        $this->payments[] = [
            'payment_date' => now()->format('Y-m-d'),
            'amount'       => 0,
            'method'       => '',
            'reference'    => '',
            'payment_proof'=> null,
        ];
    }

    public function removePaymentRow($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
    }

    public function getTotalPaymentProperty()
    {
        return collect($this->payments)->sum(function ($p) {
            return (float) ($p['amount'] ?? 0);
        });
    }

    public function getBalanceProperty()
    {
        return $this->grandTotal - $this->totalPayment;
    }

    /* ===============================
       SELECT PRODUCT
    ===============================*/

    public function selectProduct($data)
    {
        $index = $data['index'];

        $this->items[$index]['product_id']   = $data['id'];
        $this->items[$index]['product_name'] = $data['text'];
        $this->items[$index]['price']        = (float) $data['price'];

        $this->recalculateRow($index);
    }

    /* ===============================
       RECALCULATION
    ===============================*/

    public function recalculateRow($index)
    {
        $qty      = (float) ($this->items[$index]['qty'] ?? 0);
        $price    = (float) ($this->items[$index]['price'] ?? 0);
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
            'patient_id'        => 'required',
            'sale_date'         => 'required',
            'items.*.product_id'=> 'required',
            'payments.*.payment_proof' => 'nullable|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $paidTotal = $this->totalPayment;
        $balance   = $this->grandTotal - $paidTotal;

        if ($balance < 0) {

            $this->dispatch('swal',
                icon: 'error',
                title: 'Validation Failed',
                text: 'Overpayment bos'
            );

            return; // ⬅ STOP TOTAL METHOD
        }

        DB::transaction(function () {

            $paidTotal = $this->totalPayment;
            $balance   = $this->grandTotal - $paidTotal;

            $status = 'posted';

            if ($paidTotal > 0) {
                $status = $balance == 0 ? 'paid' : 'partial';
            }

            $this->sale->update([
                'patient_id'  => $this->patient_id,
                'sale_date'   => $this->sale_date,
                'subtotal'    => $this->subtotal,
                'discount'    => $this->discount,
                'tax'         => $this->tax,
                'grand_total' => $this->grandTotal,
                'paid_total'  => $paidTotal,
                'balance'     => $balance,
                'status'      => $status,
                'notes'       => $this->notes,
            ]);

            // delete old details
            $this->sale->details()->delete();

            foreach ($this->items as $item) {
                $this->sale->details()->create([
                    'product_id' => $item['product_id'],
                    'qty'        => $item['qty'],
                    'price'      => $item['price'],
                    'discount'   => $item['discount'],
                    'total'      => $item['total'],
                ]);
            }

            // delete old payments
            $this->sale->payments()->delete();

            foreach ($this->payments as $payment) {

                /* =========================
                BPJS → Langsung Lunas
                ==========================*/
                if ($payment['method'] === 'bpjs') {

                    $this->sale->payments()->create([
                        'payment_date'   => $payment['payment_date'],
                        'amount'         => $this->sale->grand_total,
                        'payment_method' => 'bpjs',
                        'notes'          => $payment['reference'] ?? 'BPJS Payment',
                    ]);

                    $isBpjs = true;
                    break; // stop, tidak perlu payment lain
                }

                $proofPath = null;

                if (!empty($payment['payment_proof'])) {
                    $proofPath = $payment['payment_proof']
                        ->store('payment_proofs', 'public');
                }


                /* =========================
                Payment Normal
                ==========================*/
                if ($payment['amount'] > 0) {

                    $this->sale->payments()->create([
                        'payment_date'   => $payment['payment_date'],
                        'amount'         => $payment['amount'],
                        'payment_method' => $payment['method'],
                        'notes'          => $payment['reference'],
                        'payment_proof'  => $proofPath,
                    ]);
                }
            }
        });

        return redirect()->route('transaction.sales');
    }

    public function previewProof($path)
    {
        $this->previewProofPath = $path;

        $this->dispatch('open-proof-modal');
    }

    public function render()
    {
        return view('livewire.transaction.sale.edit')
            ->layout('layouts.app', [
                'title' => 'Edit Sale',
                'subtitle' => 'Update sale transaction',
            ]);
    }
}