<?php

namespace Ant\Payment\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Ant\Core\Notifications\HtmlMailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvoiceIssued extends Notification
{
    use Queueable;

    protected $invoice;
    
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->invoice->isPaid()) {
            $subject = 'Receipt for payment '.$this->invoice->reference;
        } else {
            $subject = 'New invoice generated '.$this->invoice->reference;
        }
        $mail = (new HtmlMailMessage)
            ->subject($subject)
            ->markdown('payment::mail.invoice-issued', ['invoice' => $this->invoice]);

        return $mail;
    }
}