<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Overtime extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

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
