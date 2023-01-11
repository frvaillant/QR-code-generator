<?php
namespace App\Tests;

use App\Form\FormManager;
use PHPUnit\Framework\TestCase;

class FormManagerTest extends TestCase
{

    /**
     * @param $name
     * @return \ReflectionMethod
     * @throws \ReflectionException
     * mutate visibility of method to public in order to test privates or protected methods
     */
    protected static function getMethod($name)
    {
        $class = new \ReflectionClass('App\\Form\\FormManager');
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }


    public function testCreate()
    {
        $tests = [
            ['data' => null, 'has_errors' => false, 'filled' => false, ],
            ['data' => [], 'has_errors' => false, 'filled' => false, ],
            ['data' => ['url' => 'http://www.qrcode.com'], 'has_errors' => false, 'filled' => true, ],
            ['data' => ['url' => 'http://www.qrcode.com', 'size' => 512], 'has_errors' => false, 'filled' => true, ],
            ['data' => ['url' => 'http://www.qrcode.com', 'size' => 5120], 'has_errors' => true, 'filled' => false, ],
            ['data' => ['url' => 'http://www.qrcode.com', 'size' => 'coucou'], 'has_errors' => true, 'filled' => false, ],
            ['data' => ['url' => 'qrcode.com', 'size' => 'coucou'], 'has_errors' => true, 'filled' => false, ],
            ['data' => ['url' => ''], 'has_errors' => true, 'filled' => false, ],
        ];

        foreach ($tests as $key => $test) {
            $formManager = new FormManager($test['data']);
            $this->assertEquals($formManager->isFilled(), $test['filled'], 'erreur filled ' . $key);
            $this->assertEquals($formManager->hasNotErrors(), !$test['has_errors'], 'errors not match on ' . $key);
        }
    }

    public function testIsValidUrl(): void
    {
        $tests = [
            'http://qrcode.com' => true,
            'http://qrcode.com/' => true,
            'https://qrcode.com' => true,
            'https://qrcode.com/' => true,
            'http://qrcode.com/mapage' => true,
            'www://qrcode.com/mapage' => true,
            'http://www.qrcode.com/mapage' => true,
            'https://www.qrcode.com/mapage' => true,
            'www.qrcode.com/mapage' => false,
            'www.qrcode.com' => false,
            'www.qrcode.com/' => false,
            'qrcode.com/' => false,
            'qrcode.com' => false,
            'qrcode' => false,
        ];

        foreach ($tests as $url => $expected) {
            $formManager = new FormManager();
            $formManager->setUrl($url);
            $isValidUrl = self::getMethod('isValidUrl');
            $result = $isValidUrl->invokeArgs($formManager, [$url]);
            $this->assertEquals($result, $expected, $url . ' failed test');
        }
    }

    public function testIsValidColor(): void
    {
        $tests = [
            '#FF0000' => true,
            '#566588' => true,
            '#56af88' => true,
            'GHJGJD' => false,
            'ghsdjh' => false,
            '123456' => false,
            '12aa56' => false,
            '12aF56' => false,
            '#FFF' => false,
            '#000' => false,
            '000' => false,
            'aaa' => false,
        ];

        foreach ($tests as $color => $expected) {
            $formManager = new FormManager();
            $formManager->setUrl($color);
            $isValidUrl = self::getMethod('isValidColor');
            $result = $isValidUrl->invokeArgs($formManager, [$color]);
            $this->assertEquals($result, $expected, $color . ' failed test');
        }
    }

}