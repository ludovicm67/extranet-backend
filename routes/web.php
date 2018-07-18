<?php

Route::group(['middleware' => 'api'], function () {

  // routes for cron jobs
  Route::get('cron/sellsy_clients', 'CronController@sellsy_clients');
  Route::get('cron/sellsy_contacts', 'CronController@sellsy_contacts');
  Route::get('cron/sellsy_orders', 'CronController@sellsy_orders');
  Route::get('cron/sellsy_invoices', 'CronController@sellsy_invoices');

  Route::post('auth/login', 'AuthController@login');
  Route::post('password/reset', 'UserController@resetPassword');

  Route::group(['middleware' => ['jwt.auth']], function() {
    Route::match(['PUT', 'PATCH'], 'users/me', 'UserController@updateMe');
    Route::get('users/me', 'AuthController@me');

    Route::model('client', \App\SellsyClient::class);
    Route::model('sellsy_client', \App\SellsyClient::class);
    Route::apiResources([
      'users' => 'UserController',
      'roles' => 'RoleController',
      'types' => 'TypeController',
      'identifiers' => 'IdentifierController',
      'tags' => 'TagController',
      'clients' => 'SellsyClientController',
      'sellsy_clients' => 'SellsyClientController',
      'sellsy_contacts' => 'SellsyContactController',
      'sellsy_orders' => 'SellsyOrderController',
      'sellsy_invoices' => 'SellsyInvoiceController',
      'contacts' => 'ContactController',
    ]);

    Route::get('auth/logout', 'AuthController@logout');
  });

});
