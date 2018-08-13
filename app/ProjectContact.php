<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectContact extends Model
{
  protected $table = 'project_contacts';
  protected $fillable = [
    'project_id', 'contact_id'
  ];
}
