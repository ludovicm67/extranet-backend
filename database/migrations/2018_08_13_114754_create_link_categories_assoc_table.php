<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkCategoriesAssocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_categories_assoc', function (Blueprint $table) {
            $table->unsignedInteger('link_id');
            $table->unsignedInteger('category_id');
            $table->timestamps();

            $table
              ->foreign('link_id')
              ->references('id')
              ->on('links')
              ->onDelete('cascade');

            $table
              ->foreign('category_id')
              ->references('id')
              ->on('link_categories')
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
        Schema::dropIfExists('link_categories_assoc');
    }
}
