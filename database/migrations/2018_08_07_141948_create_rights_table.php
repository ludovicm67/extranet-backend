<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rights', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->string('name');
            $table->tinyInteger('show')->default(0);
            $table->tinyInteger('add')->default(0);
            $table->tinyInteger('edit')->default(0);
            $table->tinyInteger('delete')->default(0);
            $table->timestamps();

            $table->primary(['role_id', 'name']);

            $table
              ->foreign('role_id')
              ->references('id')
              ->on('roles')
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
        Schema::dropIfExists('rights');
    }
}
