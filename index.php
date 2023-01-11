<?php
    use App\Controller\HomeController;
    require 'vendor/autoload.php';

    $controller = new HomeController();
    $controller->index();