<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
  protected $table = 'contacts';
  protected $fillable = [
    'name', 'type_id', 'mail', 'phone', 'address', 'other'
  ];

  public function type() {
    return $this->hasOne(\App\Type::class, 'id', 'type_id');
  }
}
