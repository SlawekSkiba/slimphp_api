<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->group('/users', function (RouteCollectorProxy $group) use ($mysqli) {
    //Return users lsit
    $group->get('', function (Request $request, Response $response) use ($mysqli) {
        $stmt = $mysqli->prepare("select u.Id, u.email, u.firstName, u.LastName, p.name as Position from users u
        left join positions p on p.id = u.position_id");

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $users = array();
            
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            return JsonResponse::withJson($response, $users);
        } else {
            return JsonResponse::badRequest($response);
        }
    })->add(new OnlyAdminMiddleware());


    // Create a user
    $group->post('', function ($request, $response) use ($mysqli) {
        $data = $request->getParsedBody();

        // Validate request data
        if (!isset($data['email']) || !isset($data['firstName']) || !isset($data['lastName']) || !isset($data['position']) || !isset($data['password'])) {
            return JsonResponse::badRequest($response, 'Missing required fields');
        }

        $email = $data['email'];
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $position = $data['position'];
        $password = $data['password'];
        $salt = uniqid(); // Generate a salt

        // Hash the password
        $hashedPassword = hash('sha256', $password . $salt);
        try {
            // Prepare and execute the INSERT query
            $stmt = $mysqli->prepare("INSERT INTO users (email, firstName, lastName, position_id, password, salt) VALUES (?, ?, ?, ?, ?, ?);");
            $stmt->bind_param("ssssss", $email, $firstName, $lastName, $position, $hashedPassword, $salt);

            if ($newId = $stmt->execute()) {
                return JsonResponse::created($response, "/users/$newId");
            } else {
                return JsonResponse::badRequest($response, 'Failed to create user');
            }
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() == 1062) {
                return JsonResponse::conflict($response, "That user exists already");
            } else
                return JsonResponse::badRequest($response, $ex->getMessage());
        }
    });

    // Delete a user
    $group->delete('/{id}', function ($request, $response, array $args) use ($mysqli) {

        $userId = $args['id'];

        try {
            // Prepare and execute the INSERT query
            $stmt = $mysqli->prepare("delete from users where id = ?;");
            $stmt->bind_param("i", $userId);

            if ($stmt->execute()) {
                return JsonResponse::deleted($response);
            } else {
                return JsonResponse::badRequest($response, 'Failed to delete user');
            }
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() == 1062) {
                return JsonResponse::conflict($response, "That user exists already");
            } else
                return JsonResponse::badRequest($response, $ex->getMessage());
        }
    })->add(new OnlyAdminMiddleware());
})->add(new AuthMiddleware());
 // end of $app->group('/users'
