<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class AbstractController
{

    protected Environment $twig;
    private Response $response;

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
        $this->response = new Response();
    }

    protected function publish(string $content, $status = Response::HTTP_OK): Response
    {
        $this->response->setContent($content);
        $this->response->setStatusCode($status);
        return $this->response->send();
    }

    protected function redirectTo(string $path = '/'): Response
    {
        global $_ROUTER;
        $path = $_ROUTER->getRoutesPaths()[$path] ?? $path;
        $response = new RedirectResponse($path);
        return $response->send();
    }


}