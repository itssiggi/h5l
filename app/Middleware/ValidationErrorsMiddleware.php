<?php

namespace App\Middleware;

use App\Middleware\BaseMiddleware;

class ValidationErrorsMiddleware extends BaseMiddleware
{
    
    function __invoke($request, $response, $next)
    {
        if (isset($_SESSION)) {
            $this->container->view->getEnvironment()->addGlobal('errors', $_SESSION['errors']);
            unset($_SESSION["errors"]);
        }

        $response = $next($request, $response);
        return $response;
    }
}