<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Test database
//    $this->db;

//    phpinfo();

//    $db = $this->getContainer()->get('db'); //  Call to undefined method Slim\Container::getContainer()
    $db = $this->get('db');
    print_r ($db);

    if($this->has('db')) {
        return '$app has db';
    }
    else {
        return '$app doesn\'t have db';
    }


    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
});
