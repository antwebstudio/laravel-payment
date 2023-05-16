<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentInvoiceItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_invoice_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->foreign('invoice_id')->references('id')->on('payment_invoice');
            $table->integer('item_id')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price');
            $table->string('remark')->nullable();
            $table->string('currency')->nullable();
            $table->double('discount_value')->nullable()->default(0);
            $table->integer('discount_type')->nullable()->default(0);
            $table->integer('included_in_subtotal')->default(1);
            // $table->double('additional_discount')->nullable()->default(0);
            // $table->decimal('discount_amount')->default(0.0000);
            // $table->decimal('discount_percent')->default(0.0000);
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_invoice_item');
    }
}
