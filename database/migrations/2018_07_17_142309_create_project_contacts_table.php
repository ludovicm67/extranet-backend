<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_contacts', function (Blueprint $table) {
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('contact_id');
            $table->timestamps();

            $table
              ->foreign('project_id')
              ->references('id')
              ->on('projects')
              ->onDelete('cascade');

            $table
              ->foreign('contact_id')
              ->references('id')
              ->on('contacts')
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
        Schema::dropIfExists('project_contacts');
    }
}
