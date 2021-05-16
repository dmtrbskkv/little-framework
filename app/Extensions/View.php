<?php


namespace App\Extensions;


/**
 * Class View
 *
 * Show and configure views
 *
 * @package App\Extensions
 */
class View
{
    /**
     * @var string Actual view name
     */
    public static string $view;
    /**
     * @var array Data array for actual view
     */
    public static array $data = [];

    /**
     * View constructor.
     * @param string $view view name
     * @param array $data date for view
     */
    public function __construct(string $view, array $data = [])
    {
        View::verifyDirectoryConstant();
        $view = str_replace('.php', '', $view);

        $path = APP_DIRECTORY_VIEWS.'/'.$view.'.php';
        if(!file_exists($path)){
            exit("Cannot find View file!");
        }
        self::$view = $view;
        self::$data = array_merge(self::$data, $data);
        View::showView($view);

    }

    /**
     * @param string $template template name
     */
    public static function include(string $template){
        View::verifyDirectoryConstant();
        $path = APP_DIRECTORY_VIEWS.'/template-parts/'.$template.'.php';

        if(file_exists($path)){
            require_once $path;
        }
    }

    /**
     * Add new data to current View
     * @param string $key
     * @param mixed $value
     */
    public static function addData(string $key, $value){
        self::$data[$key] = $value;
    }

    public static function getData(string $key, $default = false){
        return self::$data[$key] ?? $default;
    }

    /**
     * Show view by name
     * @param string $view view name
     */
    public static function showView(string $view){
        View::verifyDirectoryConstant();
        require_once APP_DIRECTORY_VIEWS.'/'.$view.'.php';
        exit();
    }

    /**
     * Verify constants for current class
     */
    public static function verifyDirectoryConstant(){
        if(!defined("APP_DIRECTORY_VIEWS")){
            exit("Cannot find View directory!");
        }
    }

}