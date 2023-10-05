<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClaimingDetailsToOrderLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_lines', function (Blueprint $table) {
            $table->date('claimed_date')->nullable()->after('available_qty');
            $table->string('claiming_invoice_number',50)->nullable()->after('claimed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_lines', function (Blueprint $table) {
            $table->dropColumn('claimed_date');
            $table->dropColumn('claiming_invoice_number');
        });
    }
}
