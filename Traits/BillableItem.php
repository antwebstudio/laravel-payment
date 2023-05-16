<?php
namespace Ant\Payment\Traits;

use Ant\Invoice\Models\InvoiceItem;

trait BillableItem {
    public function billableItem() {
        return $this->morphOne(InvoiceItem::class, 'billable_item');
    }
}