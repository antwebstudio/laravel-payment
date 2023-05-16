<?php

namespace Ant\Payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInvoiceItem extends Model
{
    use HasFactory;

    public $table = "payment_invoice_item";

    protected $fillable = [
      'invoice_id',
      'item_id',
      'title',
      'description',
      'quantity',
      'unit_price',
      'remark',
      'currency',
      'discount_value',
      'discount_type',
      'included_in_subtotal',
      'additional_discount',
      'discount_amount',
      'discount_percent'
    ];

    public function getNameAttribute() {
      return $this->getName();
    }

    public function getName() {
      return $this->title;
    }

    public function total()
    {
      return $this->unit_price * $this->quantity;
    }

    public function getDisplayTotalAttribute() {
      return number_format($this->unit_price * $this->quantity, 2);
    }
}
