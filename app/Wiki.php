<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wiki extends Model
{
  protected $table = 'wikis';
  protected $fillable = [
    'title', 'content', 'user_id', 'project_id',
  ];

  public function user() {
    return $this->hasOne('App\User', 'id', 'user_id');
  }

  public function project() {
    return $this->hasOne('App\Project', 'id', 'project_id');
  }
}
