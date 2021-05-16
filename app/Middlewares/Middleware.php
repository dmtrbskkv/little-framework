<?php


namespace App\Middlewares;


use App\Extensions\Route;

/**
 * Class Middleware
 * @package App\Middlewares
 */
class Middleware extends MiddlewareAbstract
{
    /**
     * @var array default middleware
     */
    public static array $middleware = [
        AddUserToViewMiddleware::class
    ];
    /**
     * @var array|array[] middleware for URI
     */
    public static array $middleware_uris = [
        [
            DisableAuthPagesForLoggedUserMiddleware::class,
            ['/login', '/register']
        ],
        [
            AccessOnlyAdminsMiddleware::class,
            [
                '/categories',
                '/categories/add',
                '/categories/update',
                '/categories/remove',
                '/lost-items',
            ]
        ],
        [
            AccessOnlyLoggedUser::class,
            [
                '/lost-items/add',
                '/search-requests/list',
            ]
        ]
    ];

    /**
     * Main function of middleware
     */
    public static function run()
    {
        // run default middleware
        $middleware = self::$middleware;
        for ($i = 0; $i < count($middleware); $i++) {
            $middleware[$i]::run();
        }

        // run uris middlewares
        $middleware_uris = self::$middleware_uris;
        for ($i = 0; $i < count($middleware_uris); $i++) {
            $uri = Route::getURI();
            $middleware_uri_list = $middleware_uris[$i][1];

            if (!in_array($uri, $middleware_uri_list)) {
                continue;
            }
            $middleware_uris[$i][0]::run();
        }
    }
}