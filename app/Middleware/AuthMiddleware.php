<?php

namespace App\Middleware;

use App\Middleware\BaseMiddleware;

class AuthMiddleware extends BaseMiddleware
{
    public function __invoke($request, $response, $next) {

        if (!$this->container->auth->check()) {
            $this->container->flash->addMessage('error', 'Bitte einloggen');
            return $response->withRedirect($this->container->router->pathFor('auth.signin'));
        }

        $response = $next($request, $response);
        return $response;
    }
}

?>