<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/taxon/{id}', function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id'); // TODO: data security - does this sanitize the string?
    $this->logger->info("Route /taxon/$id");

//    return "Taxon id is " . $id; // debug
// id 13001562 = Mus musculus

    $db = $this->get('db');
    $sql = "
        SELECT *
        FROM mammal_msw
        WHERE MSW_ID=:id
    ";
    $statement = $db->prepare($sql);
    $statement->bindValue(":id", $id, PDO::PARAM_INT);
    $statement->execute();

    $data = $statement->fetch(); // Expecting only one row

    $taxonRes = $data;
    
    $res["jsonapi"]["version"] = "1.0";
    $res["meta"]["Source"] = "Mammal Species of the World";
    $res["data"]["type"] = "taxon";
    $res["data"]["id"] = $id;
    $res["data"]["attributes"] = $taxonRes;

    header('Content-Type: application/json');
    return json_encode($res, JSON_HEX_QUOT | JSON_HEX_TAG); // Converts " < and >"

    /*
    while($row = $PDOStatement->fetch()) {
        $data[] = $row;
    }
    */

    print_r ($data);

    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
});
