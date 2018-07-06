<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellsyInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sellsy_invoices', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('sellsy_id');
            $table->text('corpname');
            $table->text('ownerFullName');
            $table->text('status');
            $table->text('filename');
            $table->integer('fileid');
            $table->integer('nbpages');
            $table->text('ident');
            $table->text('thirdident');
            $table->text('thirdname');
            $table->integer('thirdid');
            $table->text('thirdvatnum');
            $table->integer('contactId');
            $table->text('contactName');
            $table->text('displayedDate');
            $table->text('currencysymbol');
            $table->text('subject');
            $table->text('docspeakerText');
            $table->integer('docspeakerStaffId');
            $table->text('docspeakerStaffFullName');
            $table->integer('corpaddressid');
            $table->integer('thirdaddressid');
            $table->integer('shipaddressid');
            $table->text('rowsAmount');
            $table->text('discountPercent');
            $table->text('discountAmount');
            $table->text('rowsAmountDiscounted');
            $table->text('offerAmount');
            $table->text('rowsAmountAllInc');
            $table->text('packagingsAmount');
            $table->text('shippingsAmount');
            $table->text('totalAmountTaxesFree');
            $table->text('taxesAmountSum');
            $table->text('taxesAmountDetails');
            $table->text('totalAmount');
            $table->text('useEcotaxe');
            $table->text('totalEcoTaxFree');
            $table->text('totalEcoTaxInc');
            $table->text('ecoTaxId');
            $table->text('taxBasis');
            $table->text('payDateText');
            $table->text('payDateCustom');
            $table->text('hasDeadlines');
            $table->text('payMediumsText');
            $table->text('payCheckOrderText');
            $table->text('payBankAccountText');
            $table->text('shippingNbParcels');
            $table->text('shippingWeight');
            $table->text('shippingWeightUnit');
            $table->text('shippingVolume');
            $table->text('shippingTrackingNumber');
            $table->text('shippingTrackingUrl');
            $table->text('shippingDate');
            $table->text('saveThirdPrefs');
            $table->text('displayShipAddress');
            $table->text('analyticsCode');
            $table->text('recorded');
            $table->text('recordable');
            $table->text('rateCategory');
            $table->text('isTaxesInc');
            $table->text('hasDoubleVat');
            $table->text('stockImpact');
            $table->text('isFromPresta');
            $table->text('eCommerceShopId');
            $table->text('signcoords');
            $table->text('esignID');
            $table->text('promotionid');
            $table->text('useServiceDates');
            $table->text('serviceDateStart');
            $table->text('serviceDateStop');
            $table->text('locked');
            $table->text('reconciledStatus');
            $table->text('corpid');
            $table->text('ownerid');
            $table->text('linkedtype');
            $table->text('linkedid');
            $table->text('created');
            $table->text('prefsid');
            $table->text('parentid');
            $table->text('docmapid');
            $table->text('hasVat');
            $table->text('doctypeid');
            $table->text('step');
            $table->text('isDeposit');
            $table->integer('posId');
            $table->text('dueAmount');
            $table->text('isSepaExported');
            $table->text('lastSepaExportDate');
            $table->text('currencyid');
            $table->text('currencyposition');
            $table->text('numberformat');
            $table->text('numberdecimals');
            $table->text('numberthousands');
            $table->text('numberprecision');
            $table->text('notes');
            $table->text('bankaccountid');
            $table->text('thirdRelationType');
            $table->text('auxCode');
            $table->text('thirdemail');
            $table->text('thirdtel');
            $table->text('thirdmobile');
            $table->text('lastpayment');
            $table->text('payDateCustomUnix');
            $table->text('formatted_dueAmount');
            $table->text('formatted_marge');
            $table->text('formatted_tauxMarque');
            $table->text('formatted_tauxMarge');
            $table->text('note');
            $table->text('step_color');
            $table->text('step_hex');
            $table->text('step_label');
            $table->text('step_css');
            $table->text('step_banner');
            $table->text('step_id');
            $table->text('displayed_payMediumsText');
            $table->text('formatted_totalAmount');
            $table->text('formatted_totalAmountTaxesFree');
            $table->text('formatted_created');
            $table->text('formatted_displayedDate');
            $table->text('formatted_payDateCustom');
            $table->text('formatted_serviceDateStart');
            $table->text('formatted_serviceDateStop');
            $table->text('formatted_lastSepaExportDate');
            $table->text('formatted_lastpayment');
            $table->text('noedit');
            $table->text('rateCategoryFormated');
            $table->text('publicLinkShort');
            $table->text('thirdStatus');

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
        Schema::dropIfExists('sellsy_invoices');
    }
}
