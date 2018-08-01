<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectIdentifier extends Model
{
  protected $table = 'project_identifiers';
  protected $fillable = [
    'project_id', 'identifier_id', 'value', 'confidential'
  ];

  protected $with = ['type'];

  public function type() {
    return $this->hasOne(\App\Identifier::class, 'id', 'identifier_id');
  }
}
