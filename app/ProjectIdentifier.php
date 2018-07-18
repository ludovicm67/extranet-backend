<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectIdentifier extends Model
{
  protected $table = 'project_identifiers';
  protected $fillable = [
    'project_id', 'identifier_id', 'value', 'confidential'
  ];
}
