<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectFavorite extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'project_favorites';
  protected $fillable = [
    'project_id', 'user_id'
  ];
}
