<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellsyClient extends Model
{
  protected $table = 'sellsy_clients';
  protected $fillable = [
    'sellsy_id',
    'capital',
    'logo',
    'joindate',
    'auxCode',
    'accountingCode',
    'stickyNote',
    'ident',
    'rateCategory',
    'massmailingUnsubscribed',
    'massmailingUnsubscribedSMS',
    'phoningUnsubscribed',
    'massmailingUnsubscribedMail',
    'massmailingUnsubscribedCustom',
    'lastactivity',
    'ownerid',
    'type',
    'maincontactid',
    'relationType',
    'actif',
    'pic',
    'dateTransformProspect',
    'mainContactName',
    'name',
    'tel',
    'fax',
    'email',
    'mobile',
    'apenaf',
    'rcs',
    'siret',
    'siren',
    'vat',
    'mainaddressid',
    'maindelivaddressid',
    'web',
    'corpType',
    'addr_name',
    'addr_part1',
    'addr_part2',
    'addr_zip',
    'addr_town',
    'addr_state',
    'addr_lat',
    'addr_lng',
    'addr_countrycode',
    'delivaddr_name',
    'delivaddr_part1',
    'delivaddr_part2',
    'delivaddr_zip',
    'delivaddr_town',
    'delivaddr_state',
    'delivaddr_lat',
    'delivaddr_lng',
    'delivaddr_countrycode',
    'formated_joindate',
    'formated_transformprospectdate',
    'corpid',
    'lastactivity_formatted',
    'addr_countryname',
    'mainAddress',
    'addr_geocode',
    'delivaddr_countryname',
    'delivAddress',
    'delivaddr_geocode',
    'fullName',
    'contactId',
    'contactDetails',
    'formatted_tel',
    'formatted_mobile',
    'formatted_fax',
    'owner',
    'webUrl',
  ];
  protected $hidden = [
    'logo',
    'capital',
    'auxCode',
    'accountingCode',
    'stickyNote',
    'ident',
    'rateCategory',
    'massmailingUnsubscribed',
    'massmailingUnsubscribedSMS',
    'phoningUnsubscribed',
    'massmailingUnsubscribedMail',
    'massmailingUnsubscribedCustom',
    'pic',
    'delivaddr_name',
    'delivaddr_part1',
    'delivaddr_part2',
    'delivaddr_zip',
    'delivaddr_town',
    'delivaddr_state',
    'delivaddr_lat',
    'delivaddr_lng',
    'delivaddr_countrycode',
    'apenaf',
    'rcs',
    'siret',
    'siren',
    'vat',
  ];

  public function contacts() {
    return $this->hasMany(SellsyContact::class, 'thirdid', 'sellsy_id');
  }

  public function orders() {
    return $this
      ->hasMany(SellsyOrder::class, 'thirdid', 'sellsy_id')
      ->orderBy('displayedDate', 'desc');
  }

  public function subscriptions() {
    return $this
      ->hasMany(SellsyInvoice::class, 'thirdid', 'sellsy_id')
      ->leftJoin('sellsy_orders', 'sellsy_invoices.parentid', '=', 'sellsy_orders.sellsy_id')
      ->select(['sellsy_invoices.*', 'sellsy_orders.id AS order_id'])
      ->whereNull('sellsy_orders.id')
      ->orderBy('displayedDate', 'desc');
  }

  public function projects() {
    return $this->hasMany(Project::class, 'client_id', 'id');
  }
}
