<?php

Route::group(['middleware' => 'api'], function () {

  // routes for cron jobs
  Route::get('cron/sellsy_clients', 'CronController@sellsy_clients')->name('cron.sellsy_clients');
  Route::get('cron/sellsy_contacts', 'CronController@sellsy_contacts')->name('cron.sellsy_contacts');
  Route::get('cron/sellsy_orders', 'CronController@sellsy_orders')->name('cron.sellsy_orders');
  Route::get('cron/sellsy_invoices', 'CronController@sellsy_invoices')->name('cron.sellsy_invoices');

  Route::get('auth/refresh', 'AuthController@refresh')->name('auth.refresh');
  Route::post('auth/login', 'AuthController@login')->name('auth.login');
  Route::post('password/reset', 'UserController@resetPassword')->name('password.reset');

  Route::group(['middleware' => ['jwt.auth']], function() {
    Route::match(['PUT', 'PATCH'], 'users/me', 'UserController@updateMe')->name('users.update_me');
    Route::get('users/me', 'AuthController@me')->name('users.me');

    Route::get('pdf/compta', 'PDFController@compta')->name('pdf.compta');

    Route::get('requests', 'RequestController@index')->name('requests.index');

    Route::post('leave/{leave}/accept', 'LeaveController@accept')->name('leave.accept');
    Route::post('leave/{leave}/reject', 'LeaveController@reject')->name('leave.reject');
    Route::post('expenses/{expense}/accept', 'ExpenseController@accept')->name('expenses.accept');
    Route::post('expenses/{expense}/reject', 'ExpenseController@reject')->name('expenses.reject');

    Route::post('projects/{project}/fav', 'ProjectController@fav')->name('projects.fav');
    Route::post('projects/{project}/unfav', 'ProjectController@unfav')->name('projects.unfav');
    Route::get('projects/{project}/identifiers', 'ProjectController@identifiers')->name('projects.identifiers');
    Route::post('projects/{project}/identifiers', 'ProjectController@newIdentifier')->name('projects.newIdentifier');

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
      'leave' => 'LeaveController',
      'expenses' => 'ExpenseController',
    ]);

    Route::get('auth/logout', 'AuthController@logout')->name('auth.logout');
    Route::get('team', 'UserController@team')->name('users.team');

    // avoid strange behaviors
    Route::options('{all}', function () {
      $response = Response::make('');

      $response->header('Access-Control-Allow-Origin', '*');
      $response->header('Access-Control-Allow-Headers', 'Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers');
      $response->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, DELETE');
      $response->header('X-Content-Type-Options', 'nosniff');
      $response->header('Access-Control-Allow-Credentials', 'true');

      return $response;
    })->name('options');
  });

});
