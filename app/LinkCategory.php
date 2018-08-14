<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class LinkCategory extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'link_categories';
  protected $fillable = ['name'];

  public function links() {
    return $this->belongsToMany('App\Link', 'link_categories_assoc', 'category_id');
  }
}
