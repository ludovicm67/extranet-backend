<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectIdentifier extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'project_identifiers';
  protected $fillable = [
    'project_id', 'identifier_id', 'value', 'confidential'
  ];

  protected $with = ['type'];

  public function type() {
    return $this->hasOne(\App\Identifier::class, 'id', 'identifier_id');
  }
}
