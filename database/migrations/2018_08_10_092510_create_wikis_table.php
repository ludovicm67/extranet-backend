<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWikisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wikis', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedInteger('user_id'); // last author
            $table->unsignedInteger('project_id');
            $table->timestamps();

            $table
              ->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
            $table
              ->foreign('project_id')
              ->references('id')
              ->on('projects')
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
        Schema::dropIfExists('wikis');
    }
}
