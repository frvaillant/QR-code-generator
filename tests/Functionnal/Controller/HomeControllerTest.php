<?php

namespace App\Tests\Functionnal\Controller;

use App\Controller\HomeController;
use App\Form\FormManager;
use Goutte\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class HomeControllerTest extends TestCase
{

    const ADDRESS = 'http://localhost:8000';

    public function testHomeController()
    {
        $client = new Client();
        $crawler = $client->request('GET', self::ADDRESS);
        $this->assertNotEquals(0, $crawler->getIterator()->count());
    }

    public function testPostForm()
    {
        $client = new Client();
        $crawler = $client->request('GET', self::ADDRESS);
        $form = $crawler->selectButton('Valider')->form();
        $crawler = $client->submit($form, ['url' => 'https://google.fr', 'size' => '256', 'color' => '#999999', 'light_color' => '#FFFFFF']);
        $this->assertEquals(1, $crawler->filter('.qrcode')->getIterator()->count());
    }

    public function testPostFormErrors()
    {
        $client = new Client();
        $crawler = $client->request('GET', self::ADDRESS);
        $form = $crawler->selectButton('Valider')->form();
        $crawler = $client->submit($form, ['url' => 'test', 'size' => 'aaa', 'color' => 'gdsh', 'light_color' => 'fgh']);
        $alerts = $crawler->filter('.errors')->getIterator();
        $this->assertCount(4, $alerts);
        $this->assertEquals(0, $crawler->filter('.qrcode')->getIterator()->count());
    }

}