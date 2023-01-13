<?php
    use App\Controller\HomeController;
    require 'vendor/autoload.php';

    $_ROUTER = new \App\Router\Router();

    if($_ROUTER->match()) {
        $_ROUTER->execute();
        die;
    }
    /*
    $controller = new HomeController();
    echo $controller->index();
    */