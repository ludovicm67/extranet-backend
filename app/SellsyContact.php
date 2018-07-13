<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellsyContact extends Model
{
  protected $table = 'sellsy_contacts';
  protected $fillable = [
    'sellsy_id',
    'pic',
    'name',
    'forename',
    'tel',
    'email',
    'mobile',
    'civil',
    'position',
    'birthdate',
    'thirdid',
    'fullName',
    'corpid',
    'formatted_tel',
    'formatted_mobile',
    'formatted_fax',
    'formatted_birthdate',
  ];

  protected $hidden = [
    'birthdate', 'formatted_birthdate', 'pic'
  ];

  public function client() {
    return $this->belongsTo(SellsyClient::class, 'thirdid', 'sellsy_id');
  }
}
