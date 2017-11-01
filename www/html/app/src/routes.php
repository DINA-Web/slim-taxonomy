<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/taxon/{id}', function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id');
    $this->logger->info("Route /taxon/$id");

//    return "Taxon id is " . $id; // debug
// id 13001562 = Mus musculus

    $db = $this->get('db');
    $sql = "
    SELECT *
    FROM mammal_msw
    WHERE MSW_ID = $id
    ";

    $PDOStatement = $db->query($sql);
    $data = $PDOStatement->fetch(); // Expecting only one row

    /*
    while($row = $PDOStatement->fetch()) {
        $data[] = $row;
    }
    */

    print_r ($data);

    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
});
