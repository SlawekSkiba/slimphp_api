<?php

use Firebase\JWT\JWT;
use Slim\Routing\RouteCollectorProxy;

$app->group('/auth', function (RouteCollectorProxy $group) use ($mysqli) {
    
    // Create and return auth token
    $group->post('', function ($request, $response) use ($mysqli) {
        $data = $request->getParsedBody();

        // Validate request data
        if (!isset($data['email']) || !isset($data['password'])) {
            return JsonResponse::badRequest($response, 'Missing required fields');
        }

        $email = $data['email'];
        $password = $data['password'];       
        
        // Hash the password
        
        try {
            // Prepare and execute the INSERT query
            $stmt = $mysqli->prepare("SELECT U.*, p.name positionName, p.is_admin FROM users U LEFT JOIN positions p on p.ID = U.position_id WHERE U.email = ?;");
            $stmt->bind_param("s", $email);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $dbpassword = $row['password'];
                $salt = $row['salt'];
                $hashedPassword = hash('sha256', $password . $salt);

                if($hashedPassword == $dbpassword){
                    return JsonResponse::withJson($response, generateJwtToken($row));
                }
                return JsonResponse::unauthorized($response, "Incorrect username or password");
            } else {
                return JsonResponse::badRequest($response, 'Failed to get user');
            }
        } catch (mysqli_sql_exception $ex) 
        {            
            return JsonResponse::badRequest($response, $ex->getMessage());
        }
    });

    function generateJwtToken($u) {
        global $jwtSecretKey, $jwtIssuer;

        $expire = time() + 20 + (60 * 60 * 6); // Token expiration time: 6 hours
        $tokenPayload = array(
            'iss' => $jwtIssuer,
            'sub' => $u['email'],
            'id' => $u['id'],
            'is_admin' => $u['is_admin'],
            'position' => $u['positionName'],
            'name' => $u['firstName']." ". $u['lastName'],
            'iat' => time(),
            'exp' => $expire // Token expiration time: 1 hour
        );     
        $result['expire'] = $expire;   
        $result['token'] = JWT::encode($tokenPayload, $jwtSecretKey, 'HS256');
        return $result;
    }    
}); 
