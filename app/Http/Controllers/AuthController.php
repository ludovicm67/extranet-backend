<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use JWTAuth;

class AuthController extends Controller
{
  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login()
  {
    $credentials = request(['email', 'password']);

    if (! $token = auth()->attempt($credentials)) {
      return response()->json([
        'service' => config('app.name'),
        'success' => false,
        'error' => 'Unauthorized'
      ], 401);
    }

    return $this->respondWithToken($token);
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function me()
  {
    return response()->json([
      'success' => true,
      'data' => auth()->user()
    ]);
  }

  /**
   * Log out
   * Invalidate the token, so user cannot use it anymore
   * They have to relogin to get a new token
   *
   * @param Request $request
   */
  public function logout(Request $request) {
    $this->validate($request, ['token' => 'required']);

    try {
      JWTAuth::invalidate($request->input('token'));
      return response()->json([
        'service' => config('app.name'),
        'success' => true,
        'message' => 'Successfully logged out'
      ]);
    } catch (JWTException $e) {
      // something went wrong whilst attempting to encode the token
      return response()->json([
        'service' => config('app.name'),
        'success' => false,
        'error' => 'Failed to logout, please try again'
      ], 500);
    }
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh()
  {
    // check token
    try {
      JWTAuth::parseToken();
      $token = JWTAuth::getToken();
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'code' => 401,
        'message' => 'missing or badly formatted token',
      ], 401);
    }

    try {
      $token = JWTAuth::refresh($token);
      JWTAuth::setToken($token);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'code' => 401,
        'message' => 'expired token',
      ], 401);
    }

    try {
      $user = JWTAuth::authenticate($token);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'code' => 401,
        'message' => 'expired token',
      ], 401);
    }

    // return new token
    return response()->json([
      'success' => true,
      'data' => [
        'token' => $token,
      ],
    ]);
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function respondWithToken($token)
  {
    return response()->json([
      'service' => config('app.name'),
      'success' => true,
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 60
    ]);
  }
}
