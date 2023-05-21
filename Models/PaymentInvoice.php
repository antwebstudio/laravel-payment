<?php

namespace Ant\Payment\Models;

use Illuminate\Support\Str;
use Ant\Contact\Contracts\Contact;
use Illuminate\Support\Facades\URL;
use Ant\Payment\Contracts\BillableItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentInvoice extends Model
{
    use HasFactory;
    use \Ant\Payment\Traits\HasBillableItems;
    use \Ant\Payment\Traits\BillTrait;
    use \Ant\SerialNumber\Traits\HasSerialNumber;

    const STATUS_UNPAID = 0;
    const STATUS_ACTIVE = 0;
    const STATUS_PAID = 1;
    const STATUS_PAID_MANUALLY = 2;

    public $table = 'payment_invoice';

    protected $fillable = [
      'old_id',
      'formatted_id',
      'total_amount',
      'discount_amount',
      'service_charges_amount',
      'absorbed_service_charges',
      'tax_amount',
      'paid_amount',
      'issue_to',
      'issue_by',
      'due_date',
      'issue_date',
      'status',
      'remark',
      'billed_to',
      'organization_id',
      'billable_id',
      'billable_class_id'
    ];

    protected $appends = ['statusHtml', 'isPaid', 'paymentUrl', 'display_attendee'];

    protected $_calculatedPaidAmount;

    public function items() {
      return $this->hasMany(PaymentInvoiceItem::class, 'invoice_id');
    }

    public function payments() {
      return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function billedTo() {
      return $this->belongsTo(\Ant\Contact\Models\Contact::class, 'billed_to');
    }

    public function isFree()
    {
      return $this->total_amount == 0;
    }

    public function isPaid() {
      return $this->isFree() ? $this->status == self::STATUS_PAID : $this->getDueAmount() <= 0;
    }

    public function getDueAmount() {
      return $this->total_amount - $this->paid_amount;
    }

    public function getIsPaidAttribute() {
      return $this->isPaid();
    }
    
    public function getPaymentUrlAttribute() {
      return URL::signedRoute('invoice.payment.bank_wire', ['invoice' => $this]);
    }

    public function getReferenceAttribute()
    {
      if (isset($this->formatted_id)) {
        return $this->formatted_id;
      }
      return '#'.Str::padLeft($this->id, 5, '0');
    }

    public function getDisplayTotalAttribute() {
      return $this->total_amount;
    }

    public function markAsPaid() {
      $this->status = self::STATUS_PAID_MANUALLY;
      $this->paid_amount = $this->total_amount;
      $this->save();
    }

    public function getStatusHtmlAttribute() {
      if ($this->isPaid()) {
        return '<span class="badge badge-success">'.$this->statusText.'</span>';
      }
      return '<span class="badge">'.$this->statusText.'</span>';
    }

    public function getStatusTextAttribute() {
      if ($this->isPaid()) {
        return 'Paid';
      }
      return 'Active';
    }

    public function getDisplayDateAttribute() {
      return $this->created_at->toDateString();
    }

    public function getDisplayAttendeeAttribute() {
      if ($this->billedTo) {
        return $this->billedTo->display_name;
      }
    }

    public function getCalculatedPaidAmount() {
      if (!isset($this->_calculatedPaidAmount)) {
        $paidAmount = 0;
        $payments = $this->payments;
        
        if (isset($payments)) {
          foreach ($payments as $payment) {
            if ($payment->isValid()) {
              $paidAmount += $payment->amount;
            }
          }
        }
        $this->_calculatedPaidAmount = $paidAmount;
      }
      if ($this->status != self::STATUS_PAID_MANUALLY && (double) $this->_calculatedPaidAmount != (double) $this->paid_amount) throw new \Exception('Paid amount recorded in database is not correct. (Invoice ID: '.$this->id.', recorded: '.$this->paid_amount.', calculated: '.$this->_calculatedPaidAmount.')');
  
      return $this->_calculatedPaidAmount;
    }
	
    public function cancelPayment($payment) {
      $payment = is_object($payment) ? $payment : Payment::find($payment);
      if (!isset($payment)) throw new \Exception('Payment not exist. ');
      
      $this->pay(0 - $payment->amount);
      
      // $this->trigger(self::EVENT_PAYMENT_CANCELED);
        
      return $this;
    } 

    public function pay($amount) {
      $this->paid_amount += $amount;

      $this->_calculatedPaidAmount = null;

      if ($this->paid_amount == $this->total_amount) {
        // $this->trigger(self::EVENT_PAID);
      }
      
      if ($this->getDueAmount() <= $amount) {
        $this->status = self::STATUS_PAID;
      }
      $this->save();

      $this->validateAmount();
      
      return $this;
    }

    public function billTo(Contact $contact) {
      $this->billedTo()->associate($contact);
    }

    public function validateAmount() {
      $this->getCalculatedPaidAmount();
    }

    protected function makeItem($title, $unitPrice, $quantity) {
      return $this->items()->make([
        'title' => $title,
        'quantity' => $quantity,
        'unit_price' => $unitPrice,
        'discount_value' => 0,
        'discount_type' => 0,
        'included_in_subtotal' => 1,
        'additional_discount' => 0,
        'discount_amount' => 0.00,
        'discount_percent' => 0.00
      ]);
    }

    public function addItemWithCustomPrice(BillableItem $item, $unitPrice, $quantity = 1) {
      $invoiceItem = $this->makeItem($item->getName(), $unitPrice, $quantity);
      
      $invoiceItem->save();
      $invoiceItem->item()->associate($item);
      $invoiceItem->save();

      $this->load('items');

      $this->recalculateTotalAmount();

      return $invoiceItem;
    }

    public function addItem(BillableItem $item, $quantity = 1) {
      $invoiceItem = $this->makeItem($item->getName(), $item->getUnitPrice(), $quantity);
      
      $invoiceItem->save();
      $invoiceItem->item()->associate($item);
      $invoiceItem->save();

      $this->load('items');

      $this->recalculateTotalAmount();

      return $invoiceItem;
    }

    public function addItemLine($title, $unitPrice, $quantity = 1) {
      $invoiceItem = $this->makeItem($title, $unitPrice, $quantity);
      $invoiceItem->save();

      $this->load('items');
      
      $this->recalculateTotalAmount();

      return $invoiceItem;

    }

    protected static function newFactory() {
      return new \Database\Factories\InvoiceFactory();
    }

    protected function getSerialNumberColumn()
    {
      return 'formatted_id';
    }

    protected static function getSerialNumberType()
    {
        return 'invoice';
    }
}
