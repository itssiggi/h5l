<?php

namespace App\Middleware;

use App\Middleware\BaseMiddleware;

class OldInputMiddleware extends BaseMiddleware
{
    function __invoke($request, $response, $next)
    {
        if (isset($_SESSION['errors'])) {
            $this->container->view->getEnvironment()->addGlobal('old', $_SESSION['old']);
            $_SESSION['old'] = $request->getParams();
        }

        $response = $next($request, $response);
        return $response;
    }
}