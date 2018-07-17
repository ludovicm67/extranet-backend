<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('type_id')->nullable();
            $table->string('mail')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('other')->nullable();
            $table->timestamps();

            $table
              ->foreign('type_id')
              ->references('id')
              ->on('types')
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
        Schema::dropIfExists('contacts');
    }
}
