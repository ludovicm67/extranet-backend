<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Link extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'links';
  protected $fillable = [
    'user_id', 'title', 'description', 'image_url', 'url',
  ];

  public function categories() {
    return $this->belongsToMany('App\LinkCategory', 'link_categories_assoc', 'link_id', 'category_id');
  }
}
