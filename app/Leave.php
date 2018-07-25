<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
  protected $table = 'leave';
  protected $fillable = [
    'user_id', 'accepted', 'file', 'details',
    'start', 'end', 'start_time', 'end_time', 'reason'
  ];

  public function user() {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}
