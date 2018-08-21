<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Team extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'teams';
  protected $fillable = ['name', 'color'];
}
