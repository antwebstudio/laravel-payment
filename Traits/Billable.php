<?php
namespace Ant\Payment\Traits;

use Ant\Payment\Models\PaymentInvoice as Invoice;
use Illuminate\Support\Collection;

trait Billable {
    public function invoiceClassName() {
        return Invoice::class;
    }

    public function getBillableItems() : Collection {
        return [];
    }

    public function getUserId() {
        return auth()->id ?? null;
    }

    public function getBillPayerId() {
        return null;
    }

    public function invoice() {
        return $this->morphOne($this->invoiceClassName(), 'billable');
    }

    public function generateInvoice() {

        \DB::transaction(function() {

            $invoice = $this->invoice()->create($this->getInvoiceData());
            
            foreach ($this->getItems() as $orderItem) {
                $invoice->items()->create($this->getInvoiceItemData($orderItem));
            }
        });
    }

    public function updateInvoice() {
        \DB::transaction(function() {
            $this->invoice->update($this->getInvoiceData());

            foreach ($this->invoice->items as $item) {
                $item->delete();
            }

            foreach ($this->getItems() as $orderItem) {
                $this->invoice->items()->create($this->getInvoiceItemData($orderItem));
            }
        });
    }

    protected function getInvoiceData() {
        return [
            'number' => '',
            'status' => 'pending',
            'user_id' => $this->getUserId(),
            'billpayer_id' => $this->getBillPayerId(),
        ];
    }

    protected function getInvoiceItemData($orderItem) {
        return [
            'name' => $orderItem->getName(),
            'billable_item_type' => $orderItem->product_type,
            'billable_item_id' => $orderItem->product_id,
            'quantity' => $orderItem->getQuantity(),
            'price' => $orderItem->getUnitPrice(),
        ];
    }
}