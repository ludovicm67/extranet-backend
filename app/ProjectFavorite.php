<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectFavorite extends Model
{
  protected $table = 'project_favorites';
  protected $fillable = [
    'project_id', 'user_id'
  ];
}
