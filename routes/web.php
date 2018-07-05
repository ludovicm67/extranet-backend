<?php

Route::post('auth/register', 'AuthController@register');
Route::post('auth/login', 'AuthController@login');
Route::post('auth/recover', 'AuthController@recover');
Route::group(['middleware' => ['jwt.auth']], function() {
  Route::get('users/me', 'AuthController@me');
  Route::get('auth/logout', 'AuthController@logout');
  Route::get('test', function(){
    return response()->json(['foo'=>'bar']);
  });
});
