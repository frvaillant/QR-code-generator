<?php

namespace App\Tests\Functionnal\Controller;

use App\Controller\HomeController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{

    protected static function getKernelClass(): string
    {
        return 'App\Kernel';

    }

    public function testIndex()
    {
        $_SERVER['APP_ENV'] = 'dev';
        $controller = new HomeController();
        $_POST['url'] = 'coucou';
        $html = $controller->index();
        $this->assertStringContainsString('url absente ou invalide', $html);

        $_POST['url'] = 'https://www.insectes.org';
        $html = $controller->index();
        $this->assertStringNotContainsString('url absente ou invalide', $html);
    }

}