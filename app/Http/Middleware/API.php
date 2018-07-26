<?php

namespace App\Http\Middleware;

use Closure;

class API {
  public function handle($request, Closure $next)
  {
    $response = $next($request);
    $response->header('Access-Control-Allow-Origin', '*');
    $response->header('Access-Control-Allow-Headers', 'Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers');
    $response->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
    $response->header('X-Content-Type-Options', 'nosniff');
    $response->header('Access-Control-Allow-Credentials', 'true');

    return $response;
  }
}
