<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Contact extends Model
{
  use LogsActivity;
  protected static $logFillable = true;

  protected $table = 'contacts';
  protected $fillable = [
    'name', 'type_id', 'mail', 'phone', 'address', 'other'
  ];

  public function type() {
    return $this->hasOne(\App\Type::class, 'id', 'type_id');
  }

  public function projects() {
    return $this->belongsToMany('App\Project', 'project_contacts');
  }
}
