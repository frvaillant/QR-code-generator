<?php

namespace App\Router;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use App\Router\RoutesCollector;

class Router
{

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var UrlMatcher
     */
    private UrlMatcher $matcher;

    /**
     * @var RouteCollection
     */
    private RouteCollection $routes;

    /**
     * @var RoutesCollector
     */
    private RoutesCollector $routesCollector;

    /**
     * @var array
     */
    private array $routesPaths = [];

    /**
     * @var array
     */
    protected array $wordpressUrls = [];

    /**
     * @throws \ReflectionException
     */
    public function __construct()
    {
        $this->request        = Request::createFromGlobals();
        $this->routesCollector = new RoutesCollector();
        $this->initRoutes();
        $this->makeUrlMatcher();
        $this->addDebugRoute();
        $this->wordpressUrls = $this->routesCollector->getWordpressUrls();
    }

    private function addDebugRoute(): void
    {
        $this->addRoute('debug_router', new Route('/debug/router', [
            '_controller' => 'App\Router\Controller\DebugController::index'
        ]));
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->request->getPathInfo();
    }

    /**
     * @return RouteCollection
     */
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getRoutesPaths(): array
    {
        return $this->routesPaths;
    }

    /**
     * @return array
     */
    public function getWordpressUrls(): array
    {
        return $this->wordpressUrls;
    }


    /**
     * @return RouteCollection
     * @throws \ReflectionException
     */
    private function initRoutes(): void
    {
        $this->routes      = $this->routesCollector->getRoutes();
        $this->routesPaths = $this->routesCollector->getRoutesPaths();
    }

    public function addRoute(string $routeId, Route $route)
    {
        $this->routes->add($routeId, $route);
    }

    /**
     * @return UrlMatcher
     */
    private function makeUrlMatcher(): void
    {
        $context = new RequestContext();
        $context->fromRequest($this->request);
        $this->matcher = new UrlMatcher($this->getRoutes(), $context);
    }

    public function match()
    {
        try {
            $this->request->attributes->add($this->matcher->match($this->getPath()));
            return $this->matcher->match($this->getPath());
        } catch (ResourceNotFoundException $e) {
            return false;
        }
    }


    public function execute()
    {
        $controllerResolver = new ControllerResolver();
        $controller = $controllerResolver->getController($this->getRequest());
        $argumentsResolver = new ArgumentResolver();
        $arguments = $argumentsResolver->getArguments($this->getRequest(), $controller);
        call_user_func_array($controller, $arguments);
    }

}