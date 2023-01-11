<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{

    protected Environment $twig;

    public function __construct()
    {
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());
        $loader = new FilesystemLoader([__DIR__ . '/../View']);
        $this->twig   = new Environment(
            $loader,
            [
                'debug' => true,
            ]
        );
    }

    protected function publish($view, $data)
    {
        return $this->twig->render($view, $data);
    }


}