<?php

// Get database settings from an environment variable file
$envMysql = parse_ini_file("../../../../env/.env-mysql");

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => true, // Allow the web server to send the content-length header, default was false

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // Database settings
        'db' => [
            'host'   => "db",
            'user'   => $envMysql['MYSQL_USER'],
            'pass'   => $envMysql['MYSQL_PASSWORD'],
            'dbname' => $envMysql['MYSQL_DATABASE'],
        ],
    ],
];
