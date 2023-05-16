<?php
namespace Ant\Payment\Traits;

trait BillItemTrait {
    public function totalInCurrency($currency) {
        if (isset($this->exchange_rate) && $this->exchange_rate_currency == $currency) {
            return $this->total() * $this->exchange_rate;
        }
        return currency($this->total(), $this->currency, $currency, false);
    }

    public function total()
    {
        return $this->price * $this->quantity;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public static function createFromBillableItem($billableItem, $additionalAttributes = [])
    {
        return static::create(array_merge([
            // 'name' => $billableItem->odoo_name,
            'name' => $billableItem->name,
            'quantity' => $billableItem->quantity,
            'currency' => $billableItem->currency,
            'price' => $billableItem->price,
            'product_id' => $billableItem->product_id,
            'product_type' => $billableItem->product_type,
        ], $additionalAttributes));
    }
}