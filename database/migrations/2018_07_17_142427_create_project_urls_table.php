<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_urls', function (Blueprint $table) {
            $table->unsignedInteger('project_id');
            $table->string('name')->nullable();
            $table->string('value')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('project_urls');
    }
}
