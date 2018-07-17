<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectIdentifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_identifiers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('identifier_id');
            $table->text('value')->nullable();
            $table->tinyInteger('confidential')->default(0);
            $table->timestamps();

            $table
              ->foreign('project_id')
              ->references('id')
              ->on('projects')
              ->onDelete('cascade');

            $table
              ->foreign('identifier_id')
              ->references('id')
              ->on('identifiers')
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
        Schema::dropIfExists('project_identifiers');
    }
}
