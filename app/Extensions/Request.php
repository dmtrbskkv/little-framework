<?php


namespace App\Extensions;


/**
 * Class Request
 *
 * Check and verify request data
 * @package App\Extensions
 */
class Request
{
    /**
     * @var array request fields
     */
    private static array $input = [];

    /**
     * Request constructor.
     */
    public function __construct()
    {
        foreach ($_REQUEST as $key => $value) {
            if(strlen($value) == 0){
                continue;
            }
            self::$input[$key] = trim($value);
        }
    }

    /**
     * @param string $key
     * @return false|mixed
     */
    public function input(string $key, $default = '')
    {

        $value = self::$input[$key] ?? false;
        return $value !== false ? $value : $default;
    }

}