<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellsyClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellsy_clients', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('sellsy_id');
            $table->text('capital');
            $table->text('logo');
            $table->text('joindate');
            $table->text('auxCode');
            $table->text('accountingCode');
            $table->text('stickyNote');
            $table->text('ident');
            $table->text('rateCategory');
            $table->text('massmailingUnsubscribed');
            $table->text('massmailingUnsubscribedSMS');
            $table->text('phoningUnsubscribed');
            $table->text('massmailingUnsubscribedMail');
            $table->text('massmailingUnsubscribedCustom');
            $table->text('lastactivity');
            $table->integer('ownerid');
            $table->text('type');
            $table->integer('maincontactid');
            $table->text('relationType');
            $table->text('actif');
            $table->text('pic');
            $table->text('dateTransformProspect');
            $table->text('mainContactName');
            $table->text('name');
            $table->text('tel');
            $table->text('fax');
            $table->text('email');
            $table->text('mobile');
            $table->text('apenaf');
            $table->text('rcs');
            $table->text('siret');
            $table->text('siren');
            $table->text('vat');
            $table->text('mainaddressid');
            $table->text('maindelivaddressid');
            $table->text('web');
            $table->text('corpType');
            $table->text('addr_name');
            $table->text('addr_part1');
            $table->text('addr_part2');
            $table->text('addr_zip');
            $table->text('addr_town');
            $table->text('addr_state');
            $table->text('addr_lat');
            $table->text('addr_lng');
            $table->text('addr_countrycode');
            $table->text('delivaddr_name');
            $table->text('delivaddr_part1');
            $table->text('delivaddr_part2');
            $table->text('delivaddr_zip');
            $table->text('delivaddr_town');
            $table->text('delivaddr_state');
            $table->text('delivaddr_lat');
            $table->text('delivaddr_lng');
            $table->text('delivaddr_countrycode');
            $table->text('formated_joindate');
            $table->text('formated_transformprospectdate');
            $table->integer('corpid');
            $table->text('lastactivity_formatted');
            $table->text('addr_countryname');
            $table->text('mainAddress');
            $table->text('addr_geocode');
            $table->text('delivaddr_countryname');
            $table->text('delivAddress');
            $table->text('delivaddr_geocode');
            $table->text('fullName');
            $table->text('contactId');
            $table->text('contactDetails');
            $table->text('formatted_tel');
            $table->text('formatted_mobile');
            $table->text('formatted_fax');
            $table->text('owner');
            $table->text('webUrl');

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
        Schema::dropIfExists('sellsy_clients');
    }
}
