<?php
    use App\Controller\HomeController;
    require 'vendor/autoload.php';

    $controller = new HomeController();
    echo $controller->index();