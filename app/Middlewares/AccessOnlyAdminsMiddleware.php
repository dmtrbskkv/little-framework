<?php


namespace App\Middlewares;


use App\Controllers\UserController;

class AccessOnlyAdminsMiddleware extends MiddlewareAbstract
{
    public static function run()
    {
        if (!(new UserController())->isAdmin()) {
            header("Location: /");
        }
    }

}