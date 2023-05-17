<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPaymentInvoiceItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('payment_invoice_item', function (Blueprint $table) {
            $table->integer('item_id')->nullable()->change();
            $table->renameColumn('item_id', 'billable_item_id');
        });
        Schema::table('payment_invoice_item', function (Blueprint $table) {
            $table->nullableMorphs('item');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('payment_invoice_item', function (Blueprint $table) {
            $table->dropMorphs('item');
            $table->renameColumn('billable_item_id', 'item_id');
        });
    }
}
