<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
  protected $table = 'expenses';
  protected $fillable = [
    'user_id', 'accepted', 'file', 'details',
    'year', 'month', 'amount', 'type'
  ];

  public function user() {
    return $this->hasOne(User::class, 'id', 'user_id');
  }
}
