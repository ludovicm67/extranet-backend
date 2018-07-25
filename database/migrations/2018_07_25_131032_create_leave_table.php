<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->tinyInteger('accepted')->default(0);
            $table->string('file')->nullable();
            $table->text('details')->nullable();

            $table->dateTime('start')->useCurrent();
            $table->dateTime('end')->useCurrent();
            $table->unsignedInteger('start_time')->default(9);
            $table->unsignedInteger('end_time')->default(18);
            $table->float('days')->default(0); // @DEPRECATED
            $table->string('reason')->default('Autre');

            $table->timestamps();

            $table
              ->foreign('user_id')
              ->references('id')
              ->on('users')
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
        Schema::dropIfExists('leave');
    }
}
