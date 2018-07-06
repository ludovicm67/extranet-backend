<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFieldsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
          $table->string('lastname')->after('name')->nullable();
          $table->tinyInteger('is_admin')->default(0);
          $table->string('default_page')->default('/');
          $table->unsignedInteger('role_id')->nullable();
          $table->foreign('role_id')->references('id')->on('roles');
          $table->string('name')->nullable()->change();
          $table->renameColumn('name', 'firstname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname')->nullable(false)->change();
            $table->renameColumn('firstname', 'name');
            $table->dropColumn([
              'lastname', 'role_id', 'is_admin', 'default_page'
            ]);
        });
    }
}
