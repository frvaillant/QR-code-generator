<?php

namespace App\Tests\Functionnal\Controller;

use Goutte\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;


class HomeControllerTest extends TestCase
{

    const ADDRESS = 'http://localhost:8000';
    const UNIQUE_FIELD_POSITION = 0;
    const HIGH_QUALITY_RADIO_POSITION = 3;

    private function postForm($data): Crawler
    {
        $client  = new Client();
        $crawler = $client->request('GET', self::ADDRESS);
        $form    = $crawler->selectButton('Valider')->form();
        return $client->submit($form, $data);
    }

    public function testHomeController()
    {
        $client  = new Client();
        $crawler = $client->request('GET', self::ADDRESS);
        $this->assertNotEquals(0, $crawler->getIterator()->count());
    }

    public function test404()
    {
        $client  = new Client();
        $crawler = $client->request('GET', self::ADDRESS . '/qrcode');
        $this->assertEquals(0, $crawler->getIterator()->count());
    }

    public function testPostForm()
    {
        $crawler = $this->postForm(['url' => 'https://google.fr', 'size' => '256', 'color' => '#999999', 'light_color' => '#FFFFFF']);
        $this->assertEquals(1, $crawler->filter('.qrcode')->getIterator()->count());
    }

    public function testPostFormErrors()
    {
        $crawler = $this->postForm(['url' => 'test', 'size' => 'aaa', 'color' => 'gdsh', 'light_color' => 'fgh']);
        $alerts  = $crawler->filter('.errors')->getIterator();
        $this->assertCount(4, $alerts);
        $this->assertEquals(0, $crawler->filter('.qrcode')->getIterator()->count());
    }

    public function testInputsAreFilled()
    {
        $data    = ['url' => 'https://google.fr', 'size' => '512', 'color' => '#999999', 'light_color' => '#fefefe'];
        $crawler = $this->postForm($data);
        foreach ($data as $field => $testValue) {
            $value = $crawler->filter('input[name="' . $field . '"]')->getNode(self::UNIQUE_FIELD_POSITION)->attributes->getNamedItem('value')->textContent;
            $this->assertEquals($testValue, $value);
        }

        $checkedQuality = $crawler->filter('input[name="quality"]')->getNode(self::HIGH_QUALITY_RADIO_POSITION)->attributes->getNamedItem('checked');
        $this->assertNotEquals(null, $checkedQuality);
    }

}