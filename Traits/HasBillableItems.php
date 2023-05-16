<?php
namespace Ant\Payment\Traits;

trait HasBillableItems {
    public function bootHasBillableItems() {
        static::saving(function($model) {
            if (!isset($model->total_amount)) {
                $model->recalculateTotalAmount();
            }
        });
    }

    public function recalculateTotalAmount() {
        $this->total_amount = $this->items->sum(function($item) {
            return $item->total();
        });
    }
}