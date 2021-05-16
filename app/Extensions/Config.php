<?php


namespace App\Extensions;


/**
 * Class Config
 * @package App\Extensions
 */
class Config
{
    /**
     * Config constructor.
     *
     * Load configs from default folder
     */
    public function __construct()
    {
        $dir = __DIR__ . '/../../config';

        $files = scandir($dir);
        for ($i = 2; $i < count($files); $i++) {
            $path = $dir . '/' . $files[$i];
            if (file_exists($path)) {
                require_once $path;
            }
        }
    }

}