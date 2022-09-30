<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFreebiesSetupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_freebies_setups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('with_freebies')->unsigned()->default(0);
            $table->integer('max_freebies')->unsigned()->default(0);
            $table->string('status',15)->default('ACTIVE');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_freebies_setups');
    }
}
