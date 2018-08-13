<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkCategory extends Model
{
  protected $table = 'link_categories';
  protected $fillable = ['name'];
}
