<?php
namespace Ant\Payment\Traits;

trait BillTrait {
    // use \Ant\Charges\Traits\HasCharges;
    
    public function total() {
        if (isset($this->total_amount)) {
            return $this->total_amount;
        }
        return $this->totalInCurrency($this->getCurrency());
    }

    public function totalInCurrency($currency)
    {
        return $this->subtotalInCurrency($currency) + $this->getTotalChargesPrice($currency);
    }

    public function subtotal() {
        return $this->subtotalInCurrency($this->getCurrency());
    }

    public function subtotalInUserCurrency() {
        return $this->subtotalInCurrency(currency()->getUserCurrency());
    }

    public function subtotalInCurrency($currency = null) 
    {
        return $this->items->sum(function($item) use($currency) {
            return $item->totalInCurrency($currency);
        });
    }

    public function billable()
    {
        return $this->morphTo();
    }
}