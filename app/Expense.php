<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Expense extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'expenses';
  protected $fillable = [
    'user_id', 'accepted', 'file', 'details',
    'year', 'month', 'amount', 'type'
  ];

  public function user() {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}
