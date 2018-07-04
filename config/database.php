<?php

use \ludovicm67\Laravel\Multidomain\Configuration;
use \ludovicm67\Laravel\Multidomain\ConfigurationObject;

$databaseConfiguration = [
  'default' => env('DB_CONNECTION', 'mysql'),
  // removed 'connections' key here
  'migrations' => 'migrations',
  'redis' => [
    'client' => 'predis',
    'default' => [
      'host' => env('REDIS_HOST', '127.0.0.1'),
      'password' => env('REDIS_PASSWORD', null),
      'port' => env('REDIS_PORT', 6379),
      'database' => 0,
    ],
  ],
];

$config = Configuration::getInstance();
$globalConf = $config->get();
$domainConf = $config->getDomain();
$databaseConfiguration['connections'] = []; // empty array

// add default database connection if we have a domain
if (!is_null($domainConf)) {
  $databaseConf = $domainConf->get('database');
  if (!is_null($databaseConf) && is_object($databaseConf)) {
    // we create the default database connection using our specified domain
    $databaseConfiguration['connections']['mysql'] = [
      'driver' => 'mysql',
      'host' => $databaseConf->get('hostname'),
      'port' => '3306',
      'database' => $databaseConf->get('database'),
      'username' => $databaseConf->get('username'),
      'password' => $databaseConf->get('password'),
      'unix_socket' => env('DB_SOCKET', ''),
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_unicode_ci',
      'prefix' => '',
      'strict' => true,
      'engine' => null,
    ];
  }
}

// append database configuration for other domains (for migrations for example)
$supportedDomains = $globalConf->get('supported_domains');
if (!empty($supportedDomains)) $supportedDomains = $supportedDomains->get();
if (!empty($supportedDomains)) {
  foreach ($supportedDomains as $domain => $conf) {
    $databaseConf = (new ConfigurationObject($conf))->get('database');
    if (!is_null($databaseConf) && is_object($databaseConf)) {
      $databaseConfiguration['connections'][$domain] = [
        'driver' => 'mysql',
        'host' => $databaseConf->get('hostname'),
        'port' => '3306',
        'database' => $databaseConf->get('database'),
        'username' => $databaseConf->get('username'),
        'password' => $databaseConf->get('password'),
        'unix_socket' => env('DB_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
      ];
    }
  }
}

return $databaseConfiguration;
