<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Role extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'roles';
  protected $fillable = ['name'];
}
