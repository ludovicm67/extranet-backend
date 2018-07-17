<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
  protected $table = 'reset_password';
  protected $fillable = [
    'user_id', 'token'
  ];
}
