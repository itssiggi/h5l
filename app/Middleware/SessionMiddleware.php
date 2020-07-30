<?php

namespace App\Middleware;

use App\Middleware\BaseMiddleware;

class SessionMiddleware extends BaseMiddleware
{
    
    function __invoke($request, $response, $next)
    {
        $session = $this->container->get('session');
        $session->start();
        $response = $next($request, $response);
        $session->save();
        return $response;
    }
}