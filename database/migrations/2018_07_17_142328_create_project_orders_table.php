<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_orders', function (Blueprint $table) {
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('order_id');
            $table->timestamps();

            $table
              ->foreign('project_id')
              ->references('id')
              ->on('projects')
              ->onDelete('cascade');

            $table
              ->foreign('order_id')
              ->references('id')
              ->on('sellsy_orders')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_orders');
    }
}
