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

    if ("SPECIES" == $data['TaxonLevel']) {

        // Get parent
        $sql = "
            SELECT *
            FROM mammal_msw
            WHERE TaxonLevel = 'GENUS'
            AND Genus = '" . $data['Genus'] . "'
        ";
        $statement = $db->prepare($sql);
        $statement->execute();
        $parentData = $statement->fetch(); // Expecting only one row
        
        $attributes['parent']['id'] = $parentData['MSW_ID'];
        $attributes['parent']['scientific_name'] = $parentData['Genus'];
        $attributes['parent']['rank'] = strtolower($parentData['TaxonLevel']);
        
        $attributes['higherTaxa']['order'] = ucfirst(strtolower($data['Order']));
        $attributes['higherTaxa']['suborder'] = ucfirst(strtolower($data['Suborder']));
        $attributes['higherTaxa']['infraorder'] = ucfirst(strtolower($data['Infraorder']));
        $attributes['higherTaxa']['superfamily'] = $data['Superfamily'];
        $attributes['higherTaxa']['family'] = $data['Family'];
        $attributes['higherTaxa']['subfamily'] = $data['Subfamily'];
        $attributes['higherTaxa']['tribe'] = $data['Tribe'];
        $attributes['higherTaxa']['genus'] = $data['Genus'];
        $attributes['higherTaxa']['subgenus'] = $data['Subgenus'];

        $attributes['rank'] = strtolower($data['TaxonLevel']);
        $attributes['scientific_name'] = $data['Genus'] . " " . $data['Species'];
        $attributes['author'] = $data['Author'];
        $attributes['author_date'] = $data['AuthorDate'];
       
        if ("YES" == $data['ValidName']) {
            $attributes['valid_name'] = TRUE;
        }
        else {
            $attributes['valid_name'] = FALSE;
        }

        if (!empty($data["CommonName"])) {
            $attributes['verncular_names']['en'][] = $data["CommonName"];
        }
    }
//    $attributes = $data; // debug - see full data from db

    $res['jsonapi']['version'] = "1.0";
    $res['meta']['Source'] = "Mammal Species of the World";
    $res['data']['type'] = "taxon";
    $res['data']['id'] = $id;
    $res['data']['attributes'] = $attributes;

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
