<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Tag extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'tags';
  protected $fillable = ['name'];

  public function projects() {
    return $this->belongsToMany('App\Project', 'project_tags')->withPivot('value');
  }
}
