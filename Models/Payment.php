<?php

namespace Ant\Payment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    const STATUS_SUCCESS = 0;
    const STATUS_PENDING = 1;
    const STATUS_INVALID = 2;

    use HasFactory;

    protected $table = 'payment';

    protected $casts = [
        'data' => 'array',
    ];

    protected $guarded = [];

    protected $appends = ['paymentMethodName', 'statusHtml', 'displayTransactionId', 'attachments'];

    public function invoice() {
        return $this->belongsTo(PaymentInvoice::class, 'invoice_id');
    }

    public function scopeByUser($query, $user)
    {
        $userId = is_object($user) ? $user->id : $user;
        $query->where('paid_by', $userId);
    }

    public function getAttachmentsAttribute() {
        if (isset($this->data['file'])) {
            return [\Storage::url($this->data['file'])];
        }
        return [];
    }

    public function getPaymentMethodNameAttribute() {
        $name = [
            'App\PaymentGateeway\BankWire' => 'Bank Transfer',
            'ant\payment\components\FaceToFacePaymentMethod' => 'Face to face',
        ];
        return $name[$this->payment_gateway] ?? $this->payment_gateway;
    }

    public function getDisplayTransactionIdAttribute() {
        
        $noTransactionId = [
            'App\PaymentGateeway\BankWire',
            'ant\payment\components\FaceToFacePaymentMethod',
        ];

        if (in_array($this->payment_gateway, $noTransactionId)) {
            return '-';
        }
        return $this->transaction_id;
    }

    public function getStatusHtmlAttribute() {
        if ($this->status == self::STATUS_SUCCESS) {
            return '<span class="badge badge-success">'.$this->statusText.'</span>';
        } else if ($this->status == self::STATUS_INVALID) {
            return '<span class="badge badge-dark">'.$this->statusText.'</span>';
        }
        
        return '<span class="badge">'.$this->statusText.'</span>';
    }

    public function getStatusTextAttribute() {
        if ($this->status == self::STATUS_SUCCESS) {
            return 'Success';
        } else if ($this->status == self::STATUS_INVALID) {
            return 'Invalid';
        }
        return 'Pending';
    }

    public function isValid() {
        return $this->is_valid;
    }

    public function isApproved() {
        return $this->status == self::STATUS_SUCCESS && $this->is_valid;
    }

    public function approve() {
        $this->status = self::STATUS_SUCCESS;
        $this->is_valid = 1;
        $this->save();
        return $this;
    }

    public function unapprove() {
        $this->status = self::STATUS_PENDING;
        $this->is_valid = 0;
        $this->save();
        return $this;
    }
}