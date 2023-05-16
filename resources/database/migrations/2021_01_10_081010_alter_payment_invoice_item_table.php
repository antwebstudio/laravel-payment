<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPaymentInvoiceItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_invoice_item', function (Blueprint $table) {
            $table->double('additional_discount')->nullable()->default(0);
            $table->decimal('discount_amount')->default(0.0000);
            $table->decimal('discount_percent')->default(0.0000);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_invoice_item', function (Blueprint $table) {
            $table->dropColumn('additional_discount');
            $table->dropColumn('discount_amount');
            $table->dropColumn('discount_percent');
        });
    }
}
