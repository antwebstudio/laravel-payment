<?php

namespace Ant\Payment\Listeners;

use Ant\Payment\Mail\Invoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendInvoiceEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
        $invoice = $event->invoice;
        if (!$invoice->isPaid()) {
            Mail::to($invoice->billedTo->email)->send(new Invoice($invoice));
        }
    }
}
