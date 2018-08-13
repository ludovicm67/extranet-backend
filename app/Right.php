<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Right extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'rights';
  protected $fillable = [
    'role_id', 'name', 'show', 'add', 'edit', 'delete',
  ];
}
