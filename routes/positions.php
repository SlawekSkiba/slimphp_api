<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/positions', function (RouteCollectorProxy $group) use ($mysqli) {
    
    //Return users lsit
    $group->get('', function(Request $request, Response $response) use ($mysqli) {        
        $stmt = $mysqli->prepare("select * from positions");

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $positions[] = $row;
            }          

            return JsonResponse::withJson($response, $positions);
        } else {
            return JsonResponse::badRequest($response);
        }
    });
})->add(new AuthMiddleware());  // end of $app->group('/users'
