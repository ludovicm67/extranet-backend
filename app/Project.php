<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $table = 'projects';
  protected $fillable = [
    'name', 'domain', 'client_id', 'next_action', 'end_at'
  ];
}
