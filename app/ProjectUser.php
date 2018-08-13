<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectUser extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'project_users';
  protected $fillable = [
    'project_id', 'user_id'
  ];
}
