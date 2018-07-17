<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('domain')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->text('next_action')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->timestamps();

            $table
              ->foreign('client_id')
              ->references('id')
              ->on('sellsy_clients')
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
        Schema::dropIfExists('projects');
    }
}
