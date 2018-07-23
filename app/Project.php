<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $table = 'projects';
  protected $fillable = [
    'name', 'domain', 'client_id', 'next_action', 'end_at'
  ];

  public function contacts() {
    return $this->belongsToMany('App\Contact', 'project_contacts');
  }

  public function orders() {
    return $this->belongsToMany('App\SellsyOrder', 'project_orders', 'project_id', 'order_id');
  }

  public function users() {
    return $this->belongsToMany('App\User', 'project_users');
  }

  public function tags() {
    return $this->belongsToMany('App\Tag', 'project_tags')->withPivot('value');
  }

  public function urls() {
    return $this->hasMany('App\ProjectUrl');
  }
}
