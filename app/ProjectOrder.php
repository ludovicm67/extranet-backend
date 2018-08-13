<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectOrder extends Model
{
  protected $table = 'project_orders';
  protected $fillable = [
    'project_id', 'order_id'
  ];
}
