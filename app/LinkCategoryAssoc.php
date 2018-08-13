<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class LinkCategoryAssoc extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'link_categories_assoc';
  protected $fillable = ['link_id', 'category_id'];
}
