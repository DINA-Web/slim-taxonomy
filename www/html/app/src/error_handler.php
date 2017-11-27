<?php

function returnError($errorMessage, $response) {

    $errorData = Array();
    $errorData['errors'][0]['code'] = "INCORRECT_INPUT";
    $errorData['errors'][0]['title'] = $errorMessage;

    $response = $response->withJson($errorData)->withStatus(400);
    return $response;
}

