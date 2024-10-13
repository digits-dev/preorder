<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionDatesToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dateTime('paid_at')->nullable()->after('order_date');
            $table->dateTime('cancelled_at')->nullable()->after('paid_at');
            $table->unsignedInteger('cancelled_by')->nullable()->after('cancelled_at');
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
            $table->dropColumn('paid_at');
            $table->dropColumn('cancelled_at');
            $table->dropColumn('cancelled_by');
        });
    }
}
