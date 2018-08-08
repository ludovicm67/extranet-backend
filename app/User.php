<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'is_admin',
        'default_page',
        'role_id',
        'team_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $with = [
      'role'
    ];

    public function role() {
      return $this->hasOne(\App\Role::class, 'id', 'role_id');
    }

    public function team() {
      return $this->hasOne(\App\Team::class, 'id', 'team_id');
    }

    public function leave() {
      return $this->hasMany(\App\Leave::class);
    }

    public function expenses() {
      return $this->hasMany(\App\Expense::class);
    }

    public function overtime() {
      return $this->hasMany(\App\Overtime::class);
    }

    public function documents() {
      return $this->hasMany(\App\Document::class)->orderBy('date', 'desc');
    }
}
