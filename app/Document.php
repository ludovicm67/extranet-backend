<?php

namespace App;

use ludovicm67\Laravel\Multidomain\Configuration;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
  protected $table = 'documents';
  protected $fillable = [
    'user_id',
    'type',
    'date',
    'file',
    'details',
  ];

  protected $appends = ['password'];

  public function user() {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function getPasswordAttribute() {
    if ($this->type != 'pay') return null;

    $config = Configuration::getInstance();
    $domainConf = $config->getDomain();

    if (empty($domainConf)) return null;
    $pay = $domainConf->get('pay');
    if (empty($pay)) return null;
    $passwd = $pay->get('password');
    if (empty($passwd) || !is_string($passwd)) return null;
    return $passwd;
  }
}
