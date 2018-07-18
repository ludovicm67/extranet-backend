<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Identifier extends Model
{
  protected $table = 'identifiers';
  protected $fillable = ['name'];
}
