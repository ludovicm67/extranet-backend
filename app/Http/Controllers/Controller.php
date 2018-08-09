<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function needPermission($permission, $right = 'show') {
      $user = auth()->user();
      if (!$user || !$user->can($permission, $right)) {
        abort(403, 'Forbidden');
      }
    }
}
