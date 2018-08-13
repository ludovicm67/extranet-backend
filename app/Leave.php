<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'leave';
  protected $fillable = [
    'user_id', 'accepted', 'file', 'details',
    'start', 'end', 'start_time', 'end_time', 'reason', 'days',
  ];

  public function user() {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}
