<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/taxon/{id}', function (Request $request, Response $response, array $args) {

    $id = $request->getAttribute('id'); // TODO: data security - does this sanitize the string?
    $this->logger->info("Route /taxon/$id");

    require_once "taxon_model.php";
    $taxon = new Taxon($this->get('db'), $this->logger);
    $taxonData = $taxon->fetchTaxon($id);
    
    header('Content-Type: application/json');
    return json_encode($taxonData, JSON_HEX_QUOT | JSON_HEX_TAG); // Converts " < and >"

    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/taxon/', function (Request $request, Response $response, array $args) {
    $filter = $request->getQueryParam('filter'); // TODO: data security - does this sanitize the string?
    $name = $filter['name'];
    $this->logger->info("Route /taxon/&filter[name]=$name");

    require_once "taxon_model.php";
    $taxon = new Taxon($this->get('db'), $this->logger);
    $taxonData = $taxon->fetchName($name, FALSE, "partial");
    
    header('Content-Type: application/json');
    return json_encode($taxonData, JSON_HEX_QUOT | JSON_HEX_TAG); // Converts " < and >"
});

$app->get('/taxonsearch/{name}', function (Request $request, Response $response, array $args) {
    /*
    $name = $request->getAttribute('name'); // TODO: data security - does this sanitize the string?

    $filter = $request->getQueryParam('filter'); // TODO: data security - does this sanitize the string?
    $search_type = $filter['search_type'];

    $this->logger->info("Route /taxonsearch/$name");

    require_once "taxon_model.php";
    $taxon = new Taxon($this->get('db'));
    $taxonData = $taxon->fetchName($name, FALSE, $search_type);
    
    header('Content-Type: application/json');
    return json_encode($taxonData, JSON_HEX_QUOT | JSON_HEX_TAG); // Converts " < and >"
*/
    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
});




