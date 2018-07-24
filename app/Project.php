<?php

namespace App;

use App\ProjetFavorite;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $table = 'projects';
  protected $attributes = ['favorited'];
  protected $appends = ['favorited'];
  protected $fillable = [
    'name', 'domain', 'client_id', 'next_action', 'end_at'
  ];

  public function contacts() {
    return $this->belongsToMany('App\Contact', 'project_contacts')->with('type');
  }

  public function orders() {
    return $this->belongsToMany('App\SellsyOrder', 'project_orders', 'project_id', 'order_id')->with('invoices');
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

  public function client() {
    return $this->hasOne('App\SellsyClient', 'id', 'client_id');
  }

  public function getFavoritedAttribute() {
    $res = ProjectFavorite::where([
        'project_id' => $this->id,
        'user_id' => auth()->user()->id,
    ])->first();

    if (!$res) return false;
    return true;
  }
}
