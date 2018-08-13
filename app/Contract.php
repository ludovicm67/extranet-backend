<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Contract extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'contracts';
  protected $fillable = [
    'user_id', 'type', 'start_at', 'end_at'
  ];

  public function user() {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}
