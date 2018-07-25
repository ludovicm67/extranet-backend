<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
  protected $table = 'contracts';
  protected $fillable = [
    'user_id', 'type', 'start_at', 'end_at'
  ];

  public function user() {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}
