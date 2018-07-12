<?php

namespace App\Http\Middleware;

use Closure;

class API {
  public function handle($request, Closure $next)
  {
    $response = $next($request);
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Headers', 'X-Requested-With');
    $response->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    return $response;
  }
}
