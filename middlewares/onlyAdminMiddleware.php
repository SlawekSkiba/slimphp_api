<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class OnlyAdminMiddleware 
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {        
        $claims = $request->getAttribute('claims');
        if(!$claims->is_admin){
            return JsonResponse::unauthorized(new Response());
        }        
        $response = $handler->handle($request);
        return $response;
    }

}
