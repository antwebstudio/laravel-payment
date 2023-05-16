<?php

namespace Tests\Unit\Invoice;

use Tests\TestCase;
use Addons\AntFusion\Field;
use App\AntFusion\Resource;
use Addons\AntFusion\Action;
use Ant\Payment\Models\PaymentInvoice;
use App\Models\Contact;

class InvoiceTest extends TestCase
{
    public function testBillTo()
    {
        $contact = Contact::factory()->create();

        $invoice = PaymentInvoice::make();
        $invoice->billTo($contact);
        
        $invoice->save();

        $invoice->refresh();

        $this->assertTrue(strlen($contact->getFirstname()) > 0);
        $this->assertTrue(strlen($contact->getLastname()) > 0);
        $this->assertTrue(strlen($contact->getContactNumber()) > 0);
        $this->assertTrue(strlen($contact->getOrganization()) > 0);
        $this->assertTrue(strlen($contact->getEmail()) > 0);

        $this->assertEquals($contact->getFirstname(), $invoice->billedTo->getFirstname());
        $this->assertEquals($contact->getLastname(), $invoice->billedTo->getLastname());
        $this->assertEquals($contact->getContactNumber(), $invoice->billedTo->getContactNumber());
        $this->assertEquals($contact->getOrganization(), $invoice->billedTo->getOrganization());
        $this->assertEquals($contact->getEmail(), $invoice->billedTo->getEmail());
        $this->assertEquals(0, $invoice->total());
    }

    public function testAddItem()
    {
        $name = 'Test Invoice Item';
        $unitPrice = 10;

        $contact = Contact::factory()->create();
        $item = InvoiceTestBillableItem::make($name, $unitPrice);

        $invoice = PaymentInvoice::make();
        $invoice->billTo($contact);

        $invoice->save();

        $invoice->addItem($item);

        $invoice->recalculateTotalAmount();
        
        $invoice->save();

        $invoice->refresh();

        $this->assertEquals(1, $invoice->items->count());
        $this->assertEquals($unitPrice, $invoice->total());
        $this->assertEquals($name, $invoice->items->first()->getName());
    }
}

class InvoiceTestBillableItem implements \Ant\Payment\Contracts\BillableItem {
    use \Ant\Core\Traits\Makeable;

    protected $name;
    protected $unitPrice;
    protected $quantity;

    public function __construct($name, $unitPrice, $quantity = 1)
    {
        $this->name = $name;
        $this->unitPrice = $unitPrice;
        $this->quantity = $quantity;
    }

    public function getItemId()
    {
        return 1;
    }

    public function getItemType()
    {
        return static::class;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function getName()
    {
        return $this->name;
    }
}