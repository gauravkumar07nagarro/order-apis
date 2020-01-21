<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateOrdersTable will create orders table where orders will be stored
 *
 * @author Gaurav Kumar <gaurav.kumar07@nagarro.com>
 */

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('origin_lat', 100);
            $table->string('origin_long', 100);
            $table->string('dest_lat', 100);
            $table->string('dest_long', 100);
            $table->integer('distance', false, true)->length(10);
            $table->string('status', 20);
            $table->integer('created_at')->default(Carbon::now()->timestamp);
            $table->integer('updated_at')->default(Carbon::now()->timestamp);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
