<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectUrl extends Model
{
  protected $table = 'project_urls';
  protected $fillable = [
    'project_id', 'name', 'value', 'order'
  ];
}
