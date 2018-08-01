<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProjectIdentifiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_identifiers', function (Blueprint $table) {
            $table->unsignedInteger('identifier_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_identifiers', function (Blueprint $table) {
            $table->unsignedInteger('identifier_id')->nullable(false)->change();
        });
    }
}
