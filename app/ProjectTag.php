<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectTag extends Model
{
  protected $table = 'project_tags';
  protected $fillable = [
    'project_id', 'tag_id', 'value'
  ];
}
