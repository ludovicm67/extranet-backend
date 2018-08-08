<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
  protected $table = 'links';
  protected $fillable = [
    'user_id', 'title', 'description', 'image_url', 'url',
  ];
}
