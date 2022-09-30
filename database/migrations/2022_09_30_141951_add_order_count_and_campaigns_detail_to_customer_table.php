<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderCountAndCampaignsDetailToCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('campaigns_id')->unsigned()->default(0)->after('payment_methods_id');
            $table->integer('order_count')->unsigned()->default(0)->after('campaigns_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('campaigns_id');
            $table->dropColumn('order_count');
        });
    }
}
