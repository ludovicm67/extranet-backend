<?php

// routes for cron jobs
Route::get('cron/sellsy_clients', 'CronController@sellsy_clients')->name('cron.sellsy_clients');
Route::get('cron/sellsy_contacts', 'CronController@sellsy_contacts')->name('cron.sellsy_contacts');
Route::get('cron/sellsy_orders', 'CronController@sellsy_orders')->name('cron.sellsy_orders');
Route::get('cron/sellsy_invoices', 'CronController@sellsy_invoices')->name('cron.sellsy_invoices');

Route::get('auth/refresh', 'AuthController@refresh')->name('auth.refresh');
Route::post('auth/login', 'AuthController@login')->name('auth.login');
Route::post('password/reset', 'UserController@resetPassword')->name('password.reset');

Route::get('ics', 'LeaveController@ics')->name('ics');

Route::group(['middleware' => ['jwt.auth']], function() {
  Route::get('links_cat/all', 'LinkCategoryController@showAll')->name('links_cat.all');
  Route::get('links_cat/{link_category}', 'LinkCategoryController@showAllFromCategory')->name('links_cat.show');
  Route::get('links/preview', 'LinkController@preview')->name('links.preview');
  Route::post('links/preview', 'LinkController@preview')->name('links.preview.post');

  Route::get('permissions', 'RightController@permissions')->name('permissions');
  Route::get('permissions/{role}', 'RightController@rolePermissions')->name('roles.permissions');

  Route::get('search', 'SearchController@index')->name('search');
  Route::match(['PUT', 'PATCH'], 'users/me', 'UserController@updateMe')->name('users.update_me');
  Route::get('users/me', 'AuthController@me')->name('users.me');

  Route::get('pdf/compta', 'PDFController@compta')->name('pdf.compta');

  Route::get('requests', 'RequestController@index')->name('requests.index');

  Route::post('leave/{leave}/accept', 'LeaveController@accept')->name('leave.accept');
  Route::post('leave/{leave}/reject', 'LeaveController@reject')->name('leave.reject');
  Route::post('expenses/{expense}/accept', 'ExpenseController@accept')->name('expenses.accept');
  Route::post('expenses/{expense}/reject', 'ExpenseController@reject')->name('expenses.reject');

  Route::get('contacts/export', 'ContactController@export')->name('contacts.export');
  Route::get('contacts/csv', 'ContactController@csv')->name('contacts.csv');

  Route::get('projects/archived', 'ProjectController@archived')->name('projects.archived');
  Route::post('projects/{project}/fav', 'ProjectController@fav')->name('projects.fav');
  Route::post('projects/{project}/unfav', 'ProjectController@unfav')->name('projects.unfav');
  Route::post('projects/{project}/archive', 'ProjectController@archive')->name('projects.archive');
  Route::post('projects/{project}/unarchive', 'ProjectController@unarchive')->name('projects.unarchive');
  Route::get('projects/{project}/identifiers', 'ProjectController@identifiers')->name('projects.identifiers');
  Route::post('projects/{project}/identifiers', 'ProjectController@newIdentifier')->name('projects.newIdentifier');
  Route::get('project_identifier/{project_identifier}', 'ProjectController@showIdentifier')->name('projects.showIdentifier');
  Route::match(['PUT', 'PATCH'], 'project_identifier/{project_identifier}', 'ProjectController@updateIdentifier')->name('projects.updateIdentifier');
  Route::delete('project_identifier/{project_identifier}', 'ProjectController@deleteIdentifier')->name('projects.deleteIdentifier');

  Route::get('overtime/{user}', 'OvertimeController@get')->name('overtime.get');
  Route::match(['PUT', 'PATCH', 'POST'], 'overtime/{user}', 'OvertimeController@set')->name('overtime.set');

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
    'documents' => 'DocumentController',
    'links' => 'LinkController',
    'link_categories' => 'LinkCategoryController',
    'teams' => 'TeamController',
    'wikis' => 'WikiController',
  ]);

  Route::get('project_wikis/{project}', 'WikiController@indexProject')->name('projects.wikis');

  Route::get('auth/logout', 'AuthController@logout')->name('auth.logout');
  Route::get('team', 'UserController@team')->name('users.team');
});
