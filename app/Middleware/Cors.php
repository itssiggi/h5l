<?php

namespace App\Middleware;

use App\Middleware\BaseMiddleware;

class Cors extends BaseMiddleware
{
    public function __invoke($request, $response, $next) {
        $response = $next($request, $response);

        return $response
            ->withHeader('Access-Control-Allow-Origin' , '*')
            ->withHeader('Access-Control-Allow-Headers', 'Access-Control-, Origin, X-Requested-With, Content-Type, Accept, x-auth, content-type')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', true);
    }
}

?>