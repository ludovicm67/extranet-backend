<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellsyContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellsy_contacts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('sellsy_id');
            $table->string('pic');
            $table->string('name');
            $table->string('forename');
            $table->string('tel');
            $table->string('email');
            $table->string('mobile');
            $table->string('civil');
            $table->string('position');
            $table->string('birthdate');
            $table->integer('thirdid');
            $table->string('fullName');
            $table->integer('corpid');
            $table->string('formatted_tel');
            $table->string('formatted_mobile');
            $table->string('formatted_fax');
            $table->string('formatted_birthdate');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sellsy_contacts');
    }
}
