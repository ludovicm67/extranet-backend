<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use LogsActivity;
    protected static $logFillable = true;


    protected $appends = ['user_projects'];

    private $permissions = [];

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
      return $this->hasOne(\App\Role::class, 'id', 'role_id')->with('permissions');
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
      return $this->hasMany(\App\Document::class)->orderBy('date', 'desc')->orderBy('id', 'desc');
    }

    public function projects() {
      return $this->belongsToMany('App\Project', 'project_users');
    }

    private function checkPermission($permission, $right = 'show') {
      if (!in_array($right, ['show', 'add', 'edit', 'delete'])) return false;
      if (!isset($this->permissions[$permission])
        || !isset($this->permissions[$permission][$right])
      ) {
        if ($this->is_admin) return true;
        if (empty($this->role_id)) return false;

        $roles = Right::where('role_id', $this->role_id)
                    ->where('name', $permission)->get();

        if (!isset($this->permissions[$permission])) {
          $this->permissions[$permission] = [];
        }

        foreach ($roles as $role) {
          if ($role[$right] == 1) {
            $this->permissions[$permission][$right] = true;
            return true;
          }
        }

        $this->permissions[$permission][$right] = false;
        return false;
      } else {
        return $this->permissions[$permission][$right];
      }
      return false;
    }

    public function can($permission, $right = 'show') {
      return $this->checkPermission($permission, $right);
    }

    public function getUserProjectsAttribute() {
      $p = json_decode(json_encode($this->projects));
      return array_map(function ($e) {
        return $e->id;
      }, $p);
    }
}
