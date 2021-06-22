<?php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => env('ENVIRONTMENT'),
        'production' => [
            'adapter' => 'mysql',
            'host' => env('PROD_DB_HOST'),
            'name' => env('PROD_DB_NAME'),
            'user' => env('PROD_DB_USER'),
            'pass' => env('PROD_DB_PASSWORD'),
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => env('DEV_DB_HOST'),
            'name' => env('DEV_DB_NAME'),
            'user' => env('DEV_DB_USER'),
            'pass' => env('DEV_DB_PASSWORD'),
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
