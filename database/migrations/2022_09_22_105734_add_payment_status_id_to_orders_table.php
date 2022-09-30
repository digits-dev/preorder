<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            
            $table->integer('payment_statuses_id')->unsigned()->default(1)->after('order_statuses_id');
            $table->string('invoice_number',50)->nullable()->after('payment_statuses_id');
            $table->integer('claim_statuses_id')->unsigned()->default(1)->after('invoice_number');
            $table->date('claimed_date')->nullable()->after('claim_statuses_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_statuses_id');
            $table->dropColumn('claim_statuses_id');
            $table->dropColumn('invoice_number');
            $table->dropColumn('claimed_date');
        });
    }
}
