<?php

use Slim\Http\Request;
use Slim\Http\Response;

require_once "error_handler.php";

// Routes

// Get single taxon by id
$app->get('/taxon/{id}', function (Request $request, Response $response, array $args) {
    $id = $request->getAttribute('id'); // TODO: data security - does this sanitize the string?

    ($this->mylog)("NEW Route /taxon/ $id");

    require_once "taxon_model.php";
    $taxon = new Taxon($this->get('db'), $this->mylog);
    $taxonData = $taxon->fetchTaxon($id);
    
    ($this->mylog)("END");

    $response = $response->withJson($taxonData);
    return $response;
});

// Search taxa by name
$app->get('/taxon/', function (Request $request, Response $response, array $args) {
    $filter = $request->getQueryParam('filter'); // TODO: data security - does this sanitize the string?
    if (!isset($filter['name']) || empty($filter['name'])) {
        return returnError("Missing required parameter filter[name]", $response);
    }
    $name = $filter['name'];

    $search_type = $request->getQueryParam('search_type');
    $allowedSearchTypes = array("exact", "partial"); // TODO: Add fuzzy, start / Lucene standard terms?
    if (!in_array($search_type, $allowedSearchTypes)) {
        $search_type = "exact"; // default search_type
    }
    
    ($this->mylog)("NEW Route /taxon/ filter[name]=$name search_type=$search_type");

    require_once "taxon_model.php";
    $taxon = new Taxon($this->get('db'), $this->mylog);
    $taxonData = $taxon->fetchName($name, FALSE, $search_type);
    
    ($this->mylog)("END");

    $response = $response->withJson($taxonData);
    return $response;
});

$app->get('/', function (Request $request, Response $response, array $args) {

    $root = Array();
    $root["taxon_by_id"] = "/taxon/{id}";
    $root["taxon_by_name"] = "/taxon?filter['name']={name}&search_type=exact";

    $response = $response->withJson($root);
    return $response;
    
});




