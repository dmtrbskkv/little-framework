<?php


namespace App\Middlewares;


use App\Controllers\UserController;
use App\Extensions\View;

class AccessOnlyLoggedUser extends MiddlewareAbstract
{
    public static function run()
    {
        $user = View::getData('user') ? View::getData('user') : (new UserController)->getCurrentUser();
        if (!$user) {
            header("Location: /");
        }
    }

}