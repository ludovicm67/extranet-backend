<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Right extends Model
{
  protected $table = 'rights';
  protected $fillable = [
    'role_id', 'name', 'show', 'add', 'edit', 'delete',
  ];
}
