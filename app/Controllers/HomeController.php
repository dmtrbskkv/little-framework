<?php


namespace App\Controllers;


use App\Extensions\View;

class HomeController
{
    public function showHomePage(){
        return new View('home');
    }
}