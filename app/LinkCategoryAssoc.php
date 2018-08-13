<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkCategoryAssoc extends Model
{
  protected $table = 'link_categories_assoc';
  protected $fillable = ['link_id', 'category_id'];
}
