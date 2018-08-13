<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Type extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'types';
  protected $fillable = ['name'];
}
