<?php

// routes for cron jobs
Route::get('cron/sellsy_clients', 'CronController@sellsy_clients');
Route::get('cron/sellsy_contacts', 'CronController@sellsy_contacts');
Route::get('cron/sellsy_orders', 'CronController@sellsy_orders');
Route::get('cron/sellsy_invoices', 'CronController@sellsy_invoices');

Route::post('auth/register', 'AuthController@register');
Route::post('auth/login', 'AuthController@login');
Route::post('auth/recover', 'AuthController@recover');
Route::group(['middleware' => ['jwt.auth']], function() {


  Route::get('users/me', 'AuthController@me');
  Route::get('auth/logout', 'AuthController@logout');
  Route::get('test', function(){
    return response()->json(['foo' => 'bar']);
  });
});

Route::model('client', \App\SellsyClient::class);
Route::model('sellsy_client', \App\SellsyClient::class);
Route::apiResources([
  'roles' => 'RoleController',
  'clients' => 'SellsyClientController',
  'sellsy_clients' => 'SellsyClientController',
  'sellsy_contacts' => 'SellsyContactController',
  'sellsy_orders' => 'SellsyOrderController',
  'sellsy_invoices' => 'SellsyInvoiceController',
]);
