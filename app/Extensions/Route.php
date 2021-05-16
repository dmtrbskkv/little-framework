<?php


namespace App\Extensions;


/**
 * Class Route
 *
 * Application routes
 *
 * @package App\Extensions
 */
class Route
{
    /**
     * @var array
     */
    private static array $routes = [];

    /**
     * Run all routes logic
     */
    public static function run()
    {
        if (defined("APP_DIRECTORY_ROUTES")) {
            (new Route)->scanRoutes();
            (new Route)->showCurrentRouteView();
        } else {
            exit("Cannot find configuration files!");
        }
    }

    /**
     * Add new route to app
     * @param string $path url, included variables
     * @param array $controller controller class and method
     * @param string $request_method route request method
     */
    public static function add(string $path, array $controller, string $request_method = 'GET')
    {
        $route_data = [
            'path' => $path,
            'controller' => $controller[0],
            'method' => $controller[1],
            'request_method' => strtoupper($request_method)
        ];
        self::$routes[] = $route_data;
    }

    /**
     * Add new GET route to app
     * @param string $path url, included variables
     * @param array $controller controller class and method
     */
    public static function get(string $path, array $controller)
    {
        self::add($path, $controller);
    }

    /**
     * Get URI without GET query
     * @return mixed|string
     */
    public static function getURI()
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $uri = explode('?', $uri)[0];
        return preg_replace('/\/$/suix', '', $uri);
    }

    /**
     * Add new POST route to app
     * @param string $path url, included variables
     * @param array $controller controller class and method
     */
    public static function post(string $path, array $controller)
    {
        self::add($path, $controller, 'POST');
    }

    /**
     * Scan routes dir for routes
     */
    private function scanRoutes()
    {
        if (!defined("APP_DIRECTORY_ROUTES")) {
            return;
        }

        $routes = scandir(APP_DIRECTORY_ROUTES);
        for ($i = 2; $i < count($routes); $i++) {
            if (file_exists(APP_DIRECTORY_ROUTES . '/' . $routes[$i])) {
                require_once APP_DIRECTORY_ROUTES . '/' . $routes[$i];
            }
        }
    }

    /**
     * Try to find route for current URI
     */
    private function showCurrentRouteView()
    {
        $routes = self::$routes;
        $uri = self::getURI();
        $uri_explode = explode('/', $uri);
        $request_method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        for ($i = 0; $i < count($routes); $i++) {
            $controller_args = [];
            $routes_uri = preg_replace('/\/$/suix', '', $routes[$i]['path']);
            $routes_uri_explode = explode('/', $routes_uri);
            $routes_request_method = $routes[$i]['request_method'];

            if ($request_method !== $routes_request_method) {
                continue;
            }
            if (count($uri_explode) != count($routes_uri_explode)
                && $routes_uri != $uri && ($uri != '/' || $uri != '')) {
                continue;
            }

            for ($k = 0; $k < count($routes_uri_explode); $k++) {
                if ($routes_uri_explode[$k] !== $uri_explode[$k]
                    && mb_stripos($routes_uri_explode[$k], '{') === false) {
                    continue 2;
                }

                if (mb_stripos($routes_uri_explode[$k], '{') !== false) {
                    $args_key = str_replace(['{', '}'], '', $routes_uri_explode[$k]);
                    $args_value = $uri_explode[$k];
                    $controller_args[$args_key] = $args_value;
                }
            }

            $controller = new $routes[$i]['controller'];
            $controller_function = $routes[$i]['method'];
            $controller_args = array_values($controller_args);
            $controller->$controller_function(...$controller_args);
        }
    }
}