<?php

namespace App;

use App\ProjetFavorite;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'projects';
  protected $appends = ['favorited'];
  protected $fillable = [
    'name', 'domain', 'client_id', 'next_action', 'end_at', 'parent_id', 'archived',
  ];

  public function contacts() {
    return $this->belongsToMany('App\Contact', 'project_contacts')->with('type');
  }

  public function orders() {
    return $this
      ->belongsToMany('App\SellsyOrder', 'project_orders', 'project_id', 'order_id')
      ->with('invoices')
      ->orderBy('displayedDate', 'desc');
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

  public function identifiers() {
    return $this->hasMany('App\ProjectIdentifier');
  }

  public function client() {
    return $this->hasOne('App\SellsyClient', 'id', 'client_id');
  }

  public function parent() {
    return $this->hasOne('App\Project', 'id', 'parent_id');
  }

  public function getFavoritedAttribute() {
    $id = 0;
    if (isset(auth()->user()->id)) $id = auth()->user()->id;
    $res = ProjectFavorite::where([
        'project_id' => $this->id,
        'user_id' => $id,
    ])->first();

    if (!$res) return false;
    return true;
  }
}
