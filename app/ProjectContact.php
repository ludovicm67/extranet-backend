<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectContact extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'project_contacts';
  protected $fillable = [
    'project_id', 'contact_id'
  ];
}
