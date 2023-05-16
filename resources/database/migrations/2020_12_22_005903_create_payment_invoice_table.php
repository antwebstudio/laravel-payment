<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_invoice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('formatted_id')->nullable();
            $table->decimal('total_amount');
            $table->decimal('discount_amount')->default('0.0');
            $table->decimal('service_charges_amount')->default('0.0');
            $table->decimal('absorbed_service_charges')->default('0.0');
            $table->decimal('tax_amount')->default(0.00);
            $table->decimal('paid_amount')->default(0.00);
            $table->integer('issue_to')->nullable();
            $table->integer('issue_by')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('issue_date')->nullable();
            $table->integer('status')->default(0);
            $table->string('remark')->default('');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('billed_to')->nullable()->unsigned();
            $table->foreign('billed_to')->references('id')->on('contact');
            $table->integer('organization_id')->nullable()->unsigned();
            $table->integer('billable_id')->nullable()->unsigned();
            $table->integer('billable_class_id')->nullable()->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_invoice');
    }
}
