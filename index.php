<?php
    use App\Controller\HomeController;
    require 'vendor/autoload.php';
    const APP_ENV = 'prod';
    $_ROUTER = new \App\Router\Router();

    if($_ROUTER->match()) {
        $_ROUTER->execute();
        die;
    }