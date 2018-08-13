<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectTag extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'project_tags';
  protected $fillable = [
    'project_id', 'tag_id', 'value'
  ];
}
