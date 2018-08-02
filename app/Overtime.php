<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
  protected $table = 'overtime';
  protected $fillable = [
    'user_id',
    'month',
    'year',
    'volume',
    'details',
  ];

  public function user() {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}
