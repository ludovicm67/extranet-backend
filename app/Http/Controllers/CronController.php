<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \ludovicm67\Laravel\Multidomain\Configuration;
use GuzzleHttp\Client;
use Teknoo\Sellsy\Transport\Guzzle;
use Teknoo\Sellsy\Sellsy;
use App\SellsyClient;
use App\SellsyContact;
use App\SellsyOrder;
use App\SellsyInvoice;

class CronController extends Controller
{
  private $sellsy = null;
  private $nbPerPage = 5000;

  public function __construct() {
    $config = Configuration::getInstance();
    $domainConf = $config->getDomain();

    if (is_null($domainConf)) {
      return;
    }

    $sellsyConf = $domainConf->get('sellsy');

    if (is_null($sellsyConf) || !is_object($sellsyConf)) {
      return;
    }

    $guzzleClient = new Client();
    $transportBridge = new Guzzle($guzzleClient);

    $sellsy = new Sellsy(
      'https://apifeed.sellsy.com/0/',
      $sellsyConf->get('access_token'),
      $sellsyConf->get('access_token_secret'),
      $sellsyConf->get('consumer_token'),
      $sellsyConf->get('consumer_token_secret')
    );

    $sellsy->setTransport($transportBridge);
    $this->sellsy = $sellsy;
  }

  public function sellsy_clients() {
    // if Sellsy was not initialized, no need to go further..
    if (is_null($this->sellsy)) {
      return response()->json([
        'success' => false,
        'error' => 'Sellsy was not initialized',
      ]);
    }

    // specific code
    $pagenum = 1;
    $nbpages = $pagenum + 1;
    do {
      $clientsRequest = $this->sellsy
        ->Client()
        ->getList(['pagination' => [
          'nbperpage' => $this->nbPerPage,
          'pagenum' => $pagenum]
        ])
        ->getResponse();
      $nbpages = $clientsRequest['infos']['nbpages'];

      $clients = $clientsRequest['result'];
      foreach ($clients as $clientId => $client) {
        SellsyClient::updateOrCreate(
          ['sellsy_id' => $clientId],
          [
            'capital' => $client['capital'] ?? '',
            'logo' => $client['logo'] ?? '',
            'joindate' => $client['joindate'] ?? '',
            'auxCode' => $client['auxCode'] ?? '',
            'accountingCode' => $client['accountingCode'] ?? '',
            'stickyNote' => $client['stickyNote'] ?? '',
            'ident' => $client['ident'] ?? '',
            'rateCategory' => $client['rateCategory'] ?? '',
            'massmailingUnsubscribed' =>
              $client['massmailingUnsubscribed'] ?? '',
            'massmailingUnsubscribedSMS' =>
              $client['massmailingUnsubscribedSMS'] ?? '',
            'phoningUnsubscribed' =>
              $client['phoningUnsubscribed'] ?? '',
            'massmailingUnsubscribedMail' =>
              $client['massmailingUnsubscribedMail'] ?? '',
            'massmailingUnsubscribedCustom' =>
              $client['massmailingUnsubscribedCustom'] ?? '',
            'lastactivity' => $client['lastactivity'] ?? '',
            'ownerid' => $client['ownerid'] ?? null,
            'type' => $client['type'] ?? '',
            'maincontactid' => $client['maincontactid'] ?? null,
            'relationType' => $client['relationType'] ?? '',
            'actif' => $client['actif'] ?? '',
            'pic' => $client['pic'] ?? '',
            'dateTransformProspect' => $client['dateTransformProspect'] ?? '',
            'mainContactName' => $client['mainContactName'] ?? '',
            'name' => $client['name'] ?? '',
            'tel' => $client['tel'] ?? '',
            'fax' => $client['fax'] ?? '',
            'email' => $client['email'] ?? '',
            'mobile' => $client['mobile'] ?? '',
            'apenaf' => $client['apenaf'] ?? '',
            'rcs' => $client['rcs'] ?? '',
            'siret' => $client['siret'] ?? '',
            'siren' => $client['siren'] ?? '',
            'vat' => $client['vat'] ?? '',
            'mainaddressid' => $client['mainaddressid'] ?? '',
            'maindelivaddressid' => $client['maindelivaddressid'] ?? '',
            'web' => $client['web'] ?? '',
            'corpType' => $client['corpType'] ?? '',
            'addr_name' => $client['addr_name'] ?? '',
            'addr_part1' => $client['addr_part1'] ?? '',
            'addr_part2' => $client['addr_part2'] ?? '',
            'addr_zip' => $client['addr_zip'] ?? '',
            'addr_town' => $client['addr_town'] ?? '',
            'addr_state' => $client['addr_state'] ?? '',
            'addr_lat' => $client['addr_lat'] ?? '',
            'addr_lng' => $client['addr_lng'] ?? '',
            'addr_countrycode' => $client['addr_countrycode'] ?? '',
            'delivaddr_name' => $client['delivaddr_name'] ?? '',
            'delivaddr_part1' => $client['delivaddr_part1'] ?? '',
            'delivaddr_part2' => $client['delivaddr_part2'] ?? '',
            'delivaddr_zip' => $client['delivaddr_zip'] ?? '',
            'delivaddr_town' => $client['delivaddr_town'] ?? '',
            'delivaddr_state' => $client['delivaddr_state'] ?? '',
            'delivaddr_lat' => $client['delivaddr_lat'] ?? '',
            'delivaddr_lng' => $client['delivaddr_lng'] ?? '',
            'delivaddr_countrycode' => $client['delivaddr_countrycode'] ?? '',
            'formated_joindate' => $client['formated_joindate'] ?? '',
            'formated_transformprospectdate' =>
              $client['formated_transformprospectdate'] ?? '',
            'corpid' => $client['corpid'] ?? null,
            'lastactivity_formatted' => $client['lastactivity_formatted'] ?? '',
            'addr_countryname' => $client['addr_countryname'] ?? '',
            'mainAddress' => $client['mainAddress'] ?? '',
            'addr_geocode' => $client['addr_geocode'] ?? '',
            'delivaddr_countryname' => $client['delivaddr_countryname'] ?? '',
            'delivAddress' => $client['delivAddress'] ?? '',
            'delivaddr_geocode' => $client['delivaddr_geocode'] ?? '',
            'fullName' => $client['fullName'] ?? '',
            'contactId' => $client['contactId'] ?? '',
            'contactDetails' => $client['contactDetails'] ?? '',
            'formatted_tel' => $client['formatted_tel'] ?? '',
            'formatted_mobile' => $client['formatted_mobile'] ?? '',
            'formatted_fax' => $client['formatted_fax'] ?? '',
            'owner' => $client['owner'] ?? '',
            'webUrl' => $client['webUrl'] ?? ''
          ]
        );
        if (isset($client['contacts'])) {
          foreach ($client['contacts'] as $contact) {
            if (isset($contact['peopleid'])) {
              SellsyContact::updateOrCreate(
                ['sellsy_id' => $contact['peopleid']],
                [
                  'pic' => $contact['pic'] ?? '',
                  'name' => $contact['name'] ?? '',
                  'forename' =>
                    $contact['forename'] ?? '',
                  'tel' => $contact['tel'] ?? '',
                  'email' => $contact['email'] ?? '',
                  'mobile' =>
                    $contact['mobile'] ?? '',
                  'civil' => $contact['civil'] ?? '',
                  'position' =>
                    $contact['position'] ?? '',
                  'birthdate' =>
                    (
                      isset($contact['birthdate']) &&
                        $contact['birthdate'] !== 'NC.'
                    )
                      ? $contact['birthdate']
                      : '',
                  'thirdid' => $contact['thirdid'] ?? null,
                  'fullName' => $contact['fullName'] ?? '',
                  'corpid' => $contact['corpid'] ?? null,
                  'formatted_tel' => $contact['formatted_tel'] ?? '',
                  'formatted_mobile' => $contact['formatted_mobile'] ?? '',
                  'formatted_fax' =>
                    (
                      isset($contact['formatted_fax']) &&
                        $contact['formatted_fax'] !== 'N/C'
                    )
                      ? $contact['formatted_fax']
                      : '',
                  'formatted_birthdate' => $contact['formatted_birthdate'] ?? ''
                ]
              );
            }
          }
        }
      }
    } while ($pagenum++ < $nbpages);

    // just say everything was OK
    return response()->json([
      'success' => true,
    ]);
  }

  public function sellsy_contacts() {
    // if Sellsy was not initialized, no need to go further..
    if (is_null($this->sellsy)) {
      return response()->json([
        'success' => false,
        'error' => 'Sellsy was not initialized',
      ]);
    }

    // specific code
    $pagenum = 1;
    $nbpages = $pagenum + 1;
    do {
      $contactsRequest = $this->sellsy
        ->Peoples()
        ->getList(['pagination' => [
          'nbperpage' => $this->nbPerPage,
          'pagenum' => $pagenum]
        ])
        ->getResponse();
      $nbpages = $contactsRequest['infos']['nbpages'];

      $contacts = $contactsRequest['result'];
      foreach ($contacts as $contactId => $contact) {
        SellsyContact::updateOrCreate(
          ['sellsy_id' => $contactId],
          [
            'pic' => $contact['pic'] ?? '',
            'name' => $contact['name'] ?? '',
            'forename' => $contact['forename'] ?? '',
            'tel' => $contact['tel'] ?? '',
            'email' => $contact['email'] ?? '',
            'mobile' => $contact['mobile'] ?? '',
            'civil' => $contact['civil'] ?? '',
            'position' => $contact['position'] ?? '',
            'birthdate' =>
              (isset($contact['birthdate']) && $contact['birthdate'] !== 'NC.')
                ? $contact['birthdate']
                : '',
            'thirdid' =>
              $contact['thirdid'] ?? null,
            'fullName' => $contact['fullName'] ?? '',
            'corpid' => $contact['corpid'] ?? null,
            'formatted_tel' => $contact['formatted_tel'] ?? '',
            'formatted_mobile' => $contact['formatted_mobile'] ?? '',
            'formatted_fax' =>
              (
                isset($contact['formatted_fax']) &&
                  $contact['formatted_fax'] !== 'N/C'
              )
                ? $contact['formatted_fax']
                : '',
            'formatted_birthdate' => $contact['formatted_birthdate'] ?? ''
          ]
        );
      }
    } while ($pagenum++ < $nbpages);


    // just say everything was OK
    return response()->json([
      'success' => true,
    ]);
  }

  public function sellsy_orders() {
    // if Sellsy was not initialized, no need to go further..
    if (is_null($this->sellsy)) {
      return response()->json([
        'success' => false,
        'error' => 'Sellsy was not initialized',
      ]);
    }

    // specific code
    $pagenum = 1;
    $nbpages = $pagenum + 1;
    do {
      $ordersRequest = $this->sellsy
        ->Document()
        ->getList([
          'doctype' => 'order',
          'pagination' => [
            'nbperpage' => $this->nbPerPage,
            'pagenum' => $pagenum
          ]
        ])
        ->getResponse();
      $nbpages = $ordersRequest['infos']['nbpages'];

      $orders = $ordersRequest['result'];
      foreach ($orders as $orderId => $order) {
        SellsyOrder::updateOrCreate(
          ['sellsy_id' => $orderId],
          [
            'corpname' => $order['corpname'] ?? '',
            'ownerFullName' => $order['ownerFullName'] ?? '',
            'status' => $order['status'] ?? '',
            'filename' => $order['filename'] ?? '',
            'fileid' => $order['fileid'] ?? '',
            'nbpages' => $order['nbpages'] ?? '',
            'ident' => $order['ident'] ?? '',
            'thirdident' => $order['thirdident'] ?? '',
            'thirdname' => $order['thirdname'] ?? '',
            'thirdid' => $order['thirdid'] ?? '',
            'thirdvatnum' => $order['thirdvatnum'] ?? '',
            'contactId' => $order['contactId'] ?? '',
            'contactName' => $order['contactName'] ?? '',
            'displayedDate' => $order['displayedDate'] ?? '',
            'currencysymbol' => $order['currencysymbol'] ?? '',
            'subject' => $order['subject'] ?? '',
            'docspeakerText' => $order['docspeakerText'] ?? '',
            'docspeakerStaffId' => $order['docspeakerStaffId'] ?? '',
            'docspeakerStaffFullName' =>
              $order['docspeakerStaffFullName'] ?? '',
            'corpaddressid' => $order['corpaddressid'] ?? '',
            'thirdaddressid' => $order['thirdaddressid'] ?? '',
            'shipaddressid' => $order['shipaddressid'] ?? '',
            'rowsAmount' => $order['rowsAmount'] ?? '',
            'discountPercent' => $order['discountPercent'] ?? '',
            'discountAmount' => $order['discountAmount'] ?? '',
            'rowsAmountDiscounted' => $order['rowsAmountDiscounted'] ?? '',
            'offerAmount' => $order['offerAmount'] ?? '',
            'rowsAmountAllInc' => $order['rowsAmountAllInc'] ?? '',
            'packagingsAmount' => $order['packagingsAmount'] ?? '',
            'shippingsAmount' => $order['shippingsAmount'] ?? '',
            'totalAmountTaxesFree' => $order['totalAmountTaxesFree'] ?? '',
            'taxesAmountSum' => $order['taxesAmountSum'] ?? '',
            'taxesAmountDetails' => $order['taxesAmountDetails'] ?? '',
            'totalAmount' => $order['totalAmount'] ?? '',
            'useEcotaxe' => $order['useEcotaxe'] ?? '',
            'totalEcoTaxFree' => $order['totalEcoTaxFree'] ?? '',
            'totalEcoTaxInc' => $order['totalEcoTaxInc'] ?? '',
            'ecoTaxId' => $order['ecoTaxId'] ?? '',
            'taxBasis' => $order['taxBasis'] ?? '',
            'payDateText' => $order['payDateText'] ?? '',
            'payDateCustom' => $order['payDateCustom'] ?? '',
            'hasDeadlines' => $order['hasDeadlines'] ?? '',
            'payMediumsText' => $order['payMediumsText'] ?? '',
            'payCheckOrderText' => $order['payCheckOrderText'] ?? '',
            'payBankAccountText' => $order['payBankAccountText'] ?? '',
            'shippingNbParcels' => $order['shippingNbParcels'] ?? '',
            'shippingWeight' => $order['shippingWeight'] ?? '',
            'shippingWeightUnit' => $order['shippingWeightUnit'] ?? '',
            'shippingVolume' => $order['shippingVolume'] ?? '',
            'shippingTrackingNumber' => $order['shippingTrackingNumber'] ?? '',
            'shippingTrackingUrl' => $order['shippingTrackingUrl'] ?? '',
            'shippingDate' => $order['shippingDate'] ?? '',
            'saveThirdPrefs' => $order['saveThirdPrefs'] ?? '',
            'displayShipAddress' => $order['displayShipAddress'] ?? '',
            'analyticsCode' => $order['analyticsCode'] ?? '',
            'recorded' => $order['recorded'] ?? '',
            'recordable' => $order['recordable'] ?? '',
            'rateCategory' => $order['rateCategory'] ?? '',
            'isTaxesInc' => $order['isTaxesInc'] ?? '',
            'hasDoubleVat' => $order['hasDoubleVat'] ?? '',
            'stockImpact' => $order['stockImpact'] ?? '',
            'isFromPresta' => $order['isFromPresta'] ?? '',
            'eCommerceShopId' => $order['eCommerceShopId'] ?? '',
            'signcoords' => $order['signcoords'] ?? '',
            'esignID' => $order['esignID'] ?? '',
            'promotionid' => $order['promotionid'] ?? '',
            'useServiceDates' => $order['useServiceDates'] ?? '',
            'serviceDateStart' => $order['serviceDateStart'] ?? '',
            'serviceDateStop' => $order['serviceDateStop'] ?? '',
            'locked' => $order['locked'] ?? '',
            'reconciledStatus' => $order['reconciledStatus'] ?? '',
            'corpid' => $order['corpid'] ?? '',
            'ownerid' => $order['ownerid'] ?? '',
            'linkedtype' => $order['linkedtype'] ?? '',
            'linkedid' => $order['linkedid'] ?? '',
            'created' => $order['created'] ?? '',
            'prefsid' => $order['prefsid'] ?? '',
            'parentid' => $order['parentid'] ?? '',
            'docmapid' => $order['docmapid'] ?? '',
            'hasVat' => $order['hasVat'] ?? '',
            'doctypeid' => $order['doctypeid'] ?? '',
            'step' => $order['step'] ?? '',
            'doctypestep' => $order['doctypestep'] ?? '',
            'expireDate' => $order['expireDate'] ?? '',
            'showSignAndStamp' => $order['showSignAndStamp'] ?? '',
            'currencyid' => $order['currencyid'] ?? '',
            'currencyposition' => $order['currencyposition'] ?? '',
            'numberformat' => $order['numberformat'] ?? '',
            'numberdecimals' => $order['numberdecimals'] ?? '',
            'numberthousands' => $order['numberthousands'] ?? '',
            'numberprecision' => $order['numberprecision'] ?? '',
            'notes' => $order['notes'] ?? '',
            'bankaccountid' => $order['bankaccountid'] ?? '',
            'thirdRelationType' => $order['thirdRelationType'] ?? '',
            'auxCode' => $order['auxCode'] ?? '',
            'thirdemail' => $order['thirdemail'] ?? '',
            'thirdtel' => $order['thirdtel'] ?? '',
            'thirdmobile' => $order['thirdmobile'] ?? '',
            'lastpayment' => $order['lastpayment'] ?? '',
            'payDateCustomUnix' => $order['payDateCustomUnix'] ?? '',
            'third_addr_name' => $order['third_addr_name'] ?? '',
            'third_addr_part1' => $order['third_addr_part1'] ?? '',
            'third_addr_part2' => $order['third_addr_part2'] ?? '',
            'third_addr_part3' => $order['third_addr_part3'] ?? '',
            'third_addr_part4' => $order['third_addr_part4'] ?? '',
            'third_addr_zip' => $order['third_addr_zip'] ?? '',
            'third_addr_town' => $order['third_addr_town'] ?? '',
            'third_addr_countrycode' => $order['third_addr_countrycode'] ?? '',
            'ship_addr_name' => $order['ship_addr_name'] ?? '',
            'ship_addr_part1' => $order['ship_addr_part1'] ?? '',
            'ship_addr_part2' => $order['ship_addr_part2'] ?? '',
            'ship_addr_part3' => $order['ship_addr_part3'] ?? '',
            'ship_addr_part4' => $order['ship_addr_part4'] ?? '',
            'ship_addr_zip' => $order['ship_addr_zip'] ?? '',
            'ship_addr_town' => $order['ship_addr_town'] ?? '',
            'ship_addr_countrycode' => $order['ship_addr_countrycode'] ?? '',
            'note' => $order['note'] ?? '',
            'step_color' => $order['step_color'] ?? '',
            'step_hex' => $order['step_hex'] ?? '',
            'step_label' => $order['step_label'] ?? '',
            'step_css' => $order['step_css'] ?? '',
            'step_banner' => $order['step_banner'] ?? '',
            'step_id' => $order['step_id'] ?? '',
            'doctypestep_color' => $order['doctypestep_color'] ?? '',
            'doctypestep_hex' => $order['doctypestep_hex'] ?? '',
            'doctypestep_label' => $order['doctypestep_label'] ?? '',
            'doctypestep_css' => $order['doctypestep_css'] ?? '',
            'doctypestep_id' => $order['doctypestep_id'] ?? '',
            'displayed_payMediumsText' =>
              $order['displayed_payMediumsText'] ?? '',
            'formatted_totalAmount' => $order['formatted_totalAmount'] ?? '',
            'formatted_totalAmountTaxesFree' =>
              $order['formatted_totalAmountTaxesFree'] ?? '',
            'formatted_created' => $order['formatted_created'] ?? '',
            'formatted_displayedDate' =>
              $order['formatted_displayedDate'] ?? '',
            'formatted_payDateCustom' =>
              $order['formatted_payDateCustom'] ?? '',
            'formatted_serviceDateStart' =>
              $order['formatted_serviceDateStart'] ?? '',
            'formatted_serviceDateStop' =>
              $order['formatted_serviceDateStop'] ?? '',
            'formatted_lastSepaExportDate' =>
              $order['formatted_lastSepaExportDate'] ?? '',
            'formatted_lastpayment' => $order['formatted_lastpayment'] ?? '',
            'formatted_expireDate' => $order['formatted_expireDate'] ?? '',
            'noedit' => $order['noedit'] ?? '',
            'publicLinkShort' => $order['publicLinkShort'] ?? '',
            'address' => $order['address'] ?? '',
            'shippingAddress' => $order['shippingAddress'] ?? '',
            'weightFormatted' => $order['weightFormatted'] ?? '',
            'weightFormattedDisplayed' =>
              $order['weightFormattedDisplayed'] ?? '',
            'thirdStatus' => $order['thirdStatus'] ?? ''
          ]
        );
      }
    } while ($pagenum++ < $nbpages);


    // just say everything was OK
    return response()->json([
      'success' => true,
    ]);
  }

  public function sellsy_invoices() {
    // if Sellsy was not initialized, no need to go further..
    if (is_null($this->sellsy)) {
      return response()->json([
        'success' => false,
        'error' => 'Sellsy was not initialized',
      ]);
    }

    // specific code
    $pagenum = 1;
    $nbpages = $pagenum + 1;
    do {
      $invoicesRequest = $this->sellsy
        ->Document()
        ->getList([
          'doctype' => 'invoice',
          'pagination' => [
            'nbperpage' => $this->nbPerPage,
            'pagenum' => $pagenum
          ]
        ])
        ->getResponse();
      $nbpages = $invoicesRequest['infos']['nbpages'];

      $invoices = $invoicesRequest['result'];
      foreach ($invoices as $invoiceId => $invoice) {
        SellsyInvoice::updateOrCreate(
          ['sellsy_id' => $invoiceId],
          [
            'corpname' => $invoice['corpname'] ?? '',
            'ownerFullName' => $invoice['ownerFullName'] ?? '',
            'status' => $invoice['status'] ?? '',
            'filename' => $invoice['filename'] ?? '',
            'fileid' => $invoice['fileid'] ?? '',
            'nbpages' => $invoice['nbpages'] ?? '',
            'ident' => $invoice['ident'] ?? '',
            'thirdident' => $invoice['thirdident'] ?? '',
            'thirdname' => $invoice['thirdname'] ?? '',
            'thirdid' => $invoice['thirdid'] ?? '',
            'thirdvatnum' => $invoice['thirdvatnum'] ?? '',
            'contactId' => $invoice['contactId'] ?? '',
            'contactName' => $invoice['contactName'] ?? '',
            'displayedDate' => $invoice['displayedDate'] ?? '',
            'currencysymbol' => $invoice['currencysymbol'] ?? '',
            'subject' => $invoice['subject'] ?? '',
            'docspeakerText' => $invoice['docspeakerText'] ?? '',
            'docspeakerStaffId' => $invoice['docspeakerStaffId'] ?? '',
            'docspeakerStaffFullName' =>
              $invoice['docspeakerStaffFullName'] ?? '',
            'corpaddressid' => $invoice['corpaddressid'] ?? '',
            'thirdaddressid' => $invoice['thirdaddressid'] ?? '',
            'shipaddressid' => $invoice['shipaddressid'] ?? '',
            'rowsAmount' => $invoice['rowsAmount'] ?? '',
            'discountPercent' => $invoice['discountPercent'] ?? '',
            'discountAmount' => $invoice['discountAmount'] ?? '',
            'rowsAmountDiscounted' => $invoice['rowsAmountDiscounted'] ?? '',
            'offerAmount' => $invoice['offerAmount'] ?? '',
            'rowsAmountAllInc' => $invoice['rowsAmountAllInc'] ?? '',
            'packagingsAmount' => $invoice['packagingsAmount'] ?? '',
            'shippingsAmount' => $invoice['shippingsAmount'] ?? '',
            'totalAmountTaxesFree' => $invoice['totalAmountTaxesFree'] ?? '',
            'taxesAmountSum' => $invoice['taxesAmountSum'] ?? '',
            'taxesAmountDetails' => $invoice['taxesAmountDetails'] ?? '',
            'totalAmount' => $invoice['totalAmount'] ?? '',
            'useEcotaxe' => $invoice['useEcotaxe'] ?? '',
            'totalEcoTaxFree' => $invoice['totalEcoTaxFree'] ?? '',
            'totalEcoTaxInc' => $invoice['totalEcoTaxInc'] ?? '',
            'ecoTaxId' => $invoice['ecoTaxId'] ?? '',
            'taxBasis' => $invoice['taxBasis'] ?? '',
            'payDateText' => $invoice['payDateText'] ?? '',
            'payDateCustom' => $invoice['payDateCustom'] ?? '',
            'hasDeadlines' => $invoice['hasDeadlines'] ?? '',
            'payMediumsText' => $invoice['payMediumsText'] ?? '',
            'payCheckOrderText' => $invoice['payCheckOrderText'] ?? '',
            'payBankAccountText' => $invoice['payBankAccountText'] ?? '',
            'shippingNbParcels' => $invoice['shippingNbParcels'] ?? '',
            'shippingWeight' => $invoice['shippingWeight'] ?? '',
            'shippingWeightUnit' => $invoice['shippingWeightUnit'] ?? '',
            'shippingVolume' => $invoice['shippingVolume'] ?? '',
            'shippingTrackingNumber' =>
              $invoice['shippingTrackingNumber'] ?? '',
            'shippingTrackingUrl' => $invoice['shippingTrackingUrl'] ?? '',
            'shippingDate' => $invoice['shippingDate'] ?? '',
            'saveThirdPrefs' => $invoice['saveThirdPrefs'] ?? '',
            'displayShipAddress' => $invoice['displayShipAddress'] ?? '',
            'analyticsCode' => $invoice['analyticsCode'] ?? '',
            'recorded' => $invoice['recorded'] ?? '',
            'recordable' => $invoice['recordable'] ?? '',
            'rateCategory' => $invoice['rateCategory'] ?? '',
            'isTaxesInc' => $invoice['isTaxesInc'] ?? '',
            'hasDoubleVat' => $invoice['hasDoubleVat'] ?? '',
            'stockImpact' => $invoice['stockImpact'] ?? '',
            'isFromPresta' => $invoice['isFromPresta'] ?? '',
            'eCommerceShopId' => $invoice['eCommerceShopId'] ?? '',
            'signcoords' => $invoice['signcoords'] ?? '',
            'esignID' => $invoice['esignID'] ?? '',
            'promotionid' => $invoice['promotionid'] ?? '',
            'useServiceDates' => $invoice['useServiceDates'] ?? '',
            'serviceDateStart' => $invoice['serviceDateStart'] ?? '',
            'serviceDateStop' => $invoice['serviceDateStop'] ?? '',
            'locked' => $invoice['locked'] ?? '',
            'reconciledStatus' => $invoice['reconciledStatus'] ?? '',
            'corpid' => $invoice['corpid'] ?? '',
            'ownerid' => $invoice['ownerid'] ?? '',
            'linkedtype' => $invoice['linkedtype'] ?? '',
            'linkedid' => $invoice['linkedid'] ?? '',
            'created' => $invoice['created'] ?? '',
            'prefsid' => $invoice['prefsid'] ?? '',
            'parentid' => $invoice['parentid'] ?? '',
            'docmapid' => $invoice['docmapid'] ?? '',
            'hasVat' => $invoice['hasVat'] ?? '',
            'doctypeid' => $invoice['doctypeid'] ?? '',
            'step' => $invoice['step'] ?? '',
            'isDeposit' => $invoice['isDeposit'] ?? '',
            'posId' => $invoice['posId'] ?? '',
            'dueAmount' => $invoice['dueAmount'] ?? '',
            'isSepaExported' => $invoice['isSepaExported'] ?? '',
            'lastSepaExportDate' => $invoice['lastSepaExportDate'] ?? '',
            'currencyid' => $invoice['currencyid'] ?? '',
            'currencyposition' => $invoice['currencyposition'] ?? '',
            'numberformat' => $invoice['numberformat'] ?? '',
            'numberdecimals' => $invoice['numberdecimals'] ?? '',
            'numberthousands' => $invoice['numberthousands'] ?? '',
            'numberprecision' => $invoice['numberprecision'] ?? '',
            'notes' => $invoice['notes'] ?? '',
            'bankaccountid' => $invoice['bankaccountid'] ?? '',
            'thirdRelationType' => $invoice['thirdRelationType'] ?? '',
            'auxCode' => $invoice['auxCode'] ?? '',
            'thirdemail' => $invoice['thirdemail'] ?? '',
            'thirdtel' => $invoice['thirdtel'] ?? '',
            'thirdmobile' => $invoice['thirdmobile'] ?? '',
            'lastpayment' => $invoice['lastpayment'] ?? '',
            'payDateCustomUnix' => $invoice['payDateCustomUnix'] ?? '',
            'formatted_dueAmount' => $invoice['formatted_dueAmount'] ?? '',
            'formatted_marge' => $invoice['formatted_marge'] ?? '',
            'formatted_tauxMarque' => $invoice['formatted_tauxMarque'] ?? '',
            'formatted_tauxMarge' => $invoice['formatted_tauxMarge'] ?? '',
            'note' => $invoice['note'] ?? '',
            'step_color' => $invoice['step_color'] ?? '',
            'step_hex' => $invoice['step_hex'] ?? '',
            'step_label' => $invoice['step_label'] ?? '',
            'step_css' => $invoice['step_css'] ?? '',
            'step_banner' => $invoice['step_banner'] ?? '',
            'step_id' => $invoice['step_id'] ?? '',
            'displayed_payMediumsText' =>
              $invoice['displayed_payMediumsText'] ?? '',
            'formatted_totalAmount' => $invoice['formatted_totalAmount'] ?? '',
            'formatted_totalAmountTaxesFree' =>
              $invoice['formatted_totalAmountTaxesFree'] ?? '',
            'formatted_created' => $invoice['formatted_created'] ?? '',
            'formatted_displayedDate' =>
              $invoice['formatted_displayedDate'] ?? '',
            'formatted_payDateCustom' =>
              $invoice['formatted_payDateCustom'] ?? '',
            'formatted_serviceDateStart' =>
              $invoice['formatted_serviceDateStart'] ?? '',
            'formatted_serviceDateStop' =>
              $invoice['formatted_serviceDateStop'] ?? '',
            'formatted_lastSepaExportDate' =>
              $invoice['formatted_lastSepaExportDate'] ?? '',
            'formatted_lastpayment' => $invoice['formatted_lastpayment'] ?? '',
            'noedit' => $invoice['noedit'] ?? '',
            'rateCategoryFormated' => $invoice['rateCategoryFormated'] ?? '',
            'publicLinkShort' => $invoice['publicLinkShort'] ?? '',
            'thirdStatus' => $invoice['thirdStatus'] ?? ''
          ]
        );
      }
    } while ($pagenum++ < $nbpages);


    // just say everything was OK
    return response()->json([
      'success' => true,
    ]);
  }
}

