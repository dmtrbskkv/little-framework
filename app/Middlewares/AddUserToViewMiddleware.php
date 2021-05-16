<?php


namespace App\Middlewares;


use App\Controllers\UserController;
use App\Extensions\View;

class AddUserToViewMiddleware extends MiddlewareAbstract
{
    public static function run()
    {
        $user_db = (new UserController())->getCurrentUser();
        View::addData('user', $user_db);

        if ($user_db) {
            $user = [];
            $user['is_admin'] = (new UserController())->isAdmin();
            $user['is_root'] = (new UserController())->isRoot();
            $user = array_merge($user_db, $user);
            View::addData('user', $user);
        }
    }

}