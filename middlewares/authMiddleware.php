<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware 
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {        
        $token = $request->getHeader('Authorization')[0] ?? '';
        $tokenStr = substr($token, 7);        
        
        if (($tokenData = $this->isValidToken($tokenStr)) == false)                     
            return JsonResponse::unauthorized(new Response());
                    
        $request = $request->withAttribute('claims', $tokenData);        
        
        $response = $handler->handle($request);
        return $response;
    }

    private function isValidToken($token)
    {
        global $jwtIssuer;
        global $jwtSecretKey;

        try {
            if($token == null || strlen($token) < 5){
                return false;
            }
            // Verify and decode the token using your secret key              
            $decodedToken = JWT::decode($token, new Key($jwtSecretKey, 'HS256'));

            if ($decodedToken->iss !== $jwtIssuer) {                
                return false;
            }            
            return $decodedToken;
        }         
        catch (Exception $e) {
            // Token is invalid or expired            
            return false;
        }
    }
}
