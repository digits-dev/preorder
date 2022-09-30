<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('digits_code',10)->nullable();
            $table->string('upc_code',50)->nullable();
            $table->string('item_description',100)->nullable();
            $table->integer('brands_id')->unsigned();
            $table->integer('item_models_id')->unsigned()->nullable();
            $table->integer('sizes_id')->unsigned()->nullable();
            $table->integer('colors_id')->unsigned()->nullable();
            $table->string('actual_color',30)->nullable();
            $table->decimal('current_srp', 9, 2)->nullable();
            $table->integer('dtc_wh',false,true)->length(10);
            $table->integer('dtc_reserved_qty',false,true)->length(10);
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
        Schema::dropIfExists('items');
    }
}
