<?php

namespace App\Controllers\Auth;

use App\Controllers\Controller;
use App\Models\User;
use App\Models\Driver;
use Respect\Validation\Validator as v;

use Psr\Http\Message\{
    ServerRequestInterface as Request,
    ResponseInterface as Response
};

class AuthController extends Controller
{
    public function getSignOut($request, $response) {
        $this->c->auth->logout();

        return $response->withRedirect($this->c->router->pathFor('auth.signin'));
    }

    public function getSignIn($request, $response)
    {
        return $this->c->view->render($response, 'auth/login.twig');
    }

    public function postSignIn($request, $response)
    {
        
        $auth = $this->c->auth->attempt(
            $request->getParam('name'),
            $request->getParam('password')
        );

        if (!$auth) {
            $this->c->flash->addMessage('error', 'Login failed');
            return $response->withRedirect($this->c->router->pathFor('auth.signin'));
        }

        return $response->withRedirect($this->c->router->pathFor('admin.index'));
    }

    public function showMe($request, $response)
    {
        return $this->c->view->render($response, 'auth/me.twig');
    }

    public function showMeEdit($request, $response)
    {
        return $this->c->view->render($response, 'auth/me_edit.twig');
    }

    public function postMeEdit($request, $response)
    {
        $validation = $this->c->validator->validate($request, [
            'user_id' => v::notEmpty(),
            'discord' => v::noWhitespace()->notEmpty()->contains('#'),
            'name' => v::notEmpty(),
            'car_number' => v::notEmpty()->Number()->IntType()->between(0, 99)
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->c->router->pathFor('auth.signup'));
        }

        return $response->withRedirect($this->c->router->pathFor('auth.me'));

    }

    public function getSignUp($request, $response)
    {
        var_dump($request->getAttribute('csrf_value'));
        return $this->c->view->render($response, 'auth/signUp.twig');
    }

    public function postSignUp($request, $response)
    {
        $validation = $this->c->validator->validate($request, [
            'discord' => v::noWhitespace()->notEmpty(),
            'name' => v::notEmpty(),
            'password' => v::notEmpty()->noWhitespace(),
            'c_password' => v::notEmpty()->noWhitespace()
        ]);

        if ($validation->failed()) {
            return $response->withRedirect($this->c->router->pathFor('auth.signup'));
        }

        $user = User::create([
            'name' => $request->getParam('name'),
            'discord' => $request->getParam('discord'),
            'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT, ['cost' => 10])
        ]);

        if ($user) {
            $driver = Driver::create([
                'name' => $request->getParam('name'),
                'team_id' => 0,
                'short_name' => $this->slugify($request->getParam('name'))
            ]);

            $user->driver_id = $driver->id;
            $user->save();
        }

        if ($driver) {
            $this->c->auth->attempt($user->name, $request->getParam('password'));
        }

        return $response->withRedirect($this->c->router->pathFor('auth.me'));
    }

    public static function slugify($text)
    {
      // replace non letter or digits by -
      $text = preg_replace('~[^\pL\d]+~u', '-', $text);

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      // trim
      $text = trim($text, '-');

      // remove duplicate -
      $text = preg_replace('~-+~', '-', $text);

      // lowercase
      $text = strtolower($text);

      if (empty($text)) {
        return 'n-a';
      }

      return $text;
    }

}
