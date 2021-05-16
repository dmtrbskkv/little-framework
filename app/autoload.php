<?php
// Simple spl autoload register
spl_autoload_register(function ($class) {
    $class = str_replace(['App\\', '\\'], ['', DIRECTORY_SEPARATOR], $class);
    require_once __DIR__.'/'.$class . '.php';
});