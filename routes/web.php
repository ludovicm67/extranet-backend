<?php

Route::group(['middleware' => 'api'], function () {

  // routes for cron jobs
  Route::get('cron/sellsy_clients', 'CronController@sellsy_clients')
        ->name('cron.sellsy_clients');
  Route::get('cron/sellsy_contacts', 'CronController@sellsy_contacts')
        ->name('cron.sellsy_contacts');
  Route::get('cron/sellsy_orders', 'CronController@sellsy_orders')
        ->name('cron.sellsy_orders');
  Route::get('cron/sellsy_invoices', 'CronController@sellsy_invoices')
        ->name('cron.sellsy_invoices');

  Route::get('auth/refresh', 'AuthController@refresh')->name('auth.refresh');
  Route::post('auth/login', 'AuthController@login')->name('auth.login');
  Route::post('password/reset', 'UserController@resetPassword')
        ->name('password.reset');

  Route::group(['middleware' => ['jwt.auth']], function() {
    Route::match(['PUT', 'PATCH'], 'users/me', 'UserController@updateMe')
          ->name('users.update_me');
    Route::get('users/me', 'AuthController@me')
          ->name('users.me');

    Route::post('projects/{project}/fav', 'ProjectController@fav')
          ->name('projects.fav');
    Route::post('projects/{project}/unfav', 'ProjectController@unfav')
          ->name('projects.unfav');

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
      'projects' => 'ProjectController',
      'contracts' => 'ContractController',
    ]);

    Route::get('auth/logout', 'AuthController@logout')->name('auth.logout');
  });

});
