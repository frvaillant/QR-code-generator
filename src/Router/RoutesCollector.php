<?php

namespace App\Router;

use App\Router\Traits\PlurialTrait;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Doctrine\Common\Annotations\AnnotationReader as DocReader;
use Symfony\Component\Routing\Annotation\Route as SiteRoute;

/**
 * This class parses the Controller folder. It gets all *Controller.php files and extracts @Route annotations
 * Finally, il returns a RouteCollection
 */
class RoutesCollector
{
    use PlurialTrait;

    /**
     * Annotation @Route FQCN
     */
    const ROUTE_CLASS = 'Symfony\Component\Routing\Annotation\Route';

    /**
     * Files to ignore when inspecting folder
     */
    const EXCLUDED_FOLDERS = [
            'vendor',
            'assets',
            'Async',
            'AsyncTasks',
            'ChatBundle',
            'console',
            'DevTools',
            'Entity',
            'Form',
            'Import',
            'Model',
            'node_modules',
            'public',
            'Service',
            'Templates',
            'View',
    ];

    /**
     * @var string
     * Controller folder
     */
    private string $folder;

    /**
     * @var RouteCollection
     */
    private RouteCollection $routes;

    /**
     *
     */
    private array $routesPaths = [];

    /**
     * @var DocParser
     */
    private DocParser $parser;

    /**
     * @var DocReader
     */
    private DocReader $reader;

    /**
     * @var array
     */
    private array $errors = [];


    protected array $wordpressUrls = [];

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function __construct()
    {
        $this->folder = __DIR__ . '/../';
        $this->routes = new RouteCollection();
        $this->parser = new DocParser();
        $this->reader = new AnnotationReader($this->parser);
    }

    /**
     * @param $fileName
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    private function getReflectionClass($fileName, $namespace): \ReflectionClass
    {
        $className     = $namespace . '\\' . $fileName;
        return new \ReflectionClass($className);
    }


    /**
     * @param null $folder
     * @return RouteCollection
     * @throws \ReflectionException
     * @throws \Exception
     *
     * this method is recursive in case of sub folders
     */
    public function getRoutes(): RouteCollection
    {
        $finder = new Finder();
        foreach (self::EXCLUDED_FOLDERS as $folder) {
            $finder->exclude($folder);
        }
        $finder
            ->files()
            ->in($this->folder)
            ->name('*Controller.php')
            ->notName('AbstractController.php')
        ;

        foreach ($finder as $file) {
            $fileName = basename($file->getFilename(), '.php');
            preg_match('/(namespace )(.*?)(;)/', $file->getContents(), $matches);
            $namespace = $matches[2];
            $class = $this->getReflectionClass($fileName, $namespace);
            foreach ($class->getMethods() as $method) {
                $this->addRouteFromMethod($method);
            }
        }

        if($this->hasErrors()) {

            $this->dispatchErrors();

        }

        return $this->routes;
    }

    /**
     *
     */
    private function checkErrors()
    {
        $this->getAllWordpressUrl();
        $common = array_intersect_key($this->wordpressUrls, $this->routesPaths);

        foreach ($common as $key => $error) {
            $this->addError($this->wordpressUrls[$key], $this->routesPaths[$key]);
        }
    }

    /**
     * @return bool
     */
    private function hasErrors(): bool
    {
        $this->checkErrors();
        return count($this->errors) > 0;
    }

    /**
     * @return string|null
     */
    private function createErrorMessage(): ?string
    {
        $content = count($this->errors) . ' conflit' . $this->plurial($this->errors) . ' de route / permalink : ';
        foreach ($this->errors as $key => $error) {
            $num = $key + 1;
            $content .= '<br /> [' . $num . '] => ' . $error;
        }
        return $content;
    }

    /**
     * this creates an error message as txt file and adds error notice in the WP back office in order to list routes conflicts
     */
    private function dispatchErrors(): void
    {
        $this->addWordpressAdminNotice();
        if(is_user_logged_in() && current_user_can('administrator')) {
            $this->addWordpressFrontNotice();
        }
    }

    /**
     * Adds error notice in wordpress back office if routes conflicts
     */
    private function addWordpressAdminNotice()
    {
        add_action('admin_enqueue_scripts', function ($hook) {
            if ('post.php' !== $hook) {
                return;
            }
            wp_register_script( 'routes_validator_admin', get_bloginfo('template_directory') . '/Router/assets/js/adminNotice.js' );
            wp_enqueue_script( 'routes_validator_admin' );
            wp_localize_script( 'routes_validator_admin', 'my_routes_errors', ['error_message' => base64_encode(utf8_decode($this->createErrorMessage()))] );
        });
    }

    /**
     * Adds error notice in wordpress front website if routes conflicts
     */
    private function addWordpressFrontNotice()
    {
        wp_enqueue_style('notice_alert', get_bloginfo('template_directory') . '/Router/assets/css/notice.css');
        add_action('wp_enqueue_scripts', function ($hook) {
            wp_register_script( 'routes_validator_front', get_bloginfo('template_directory') . '/Router/assets/js/frontNotice.js' );
            wp_enqueue_script( 'routes_validator_front' );
            wp_localize_script( 'routes_validator_front', 'my_routes_errors', ['error_message' => base64_encode(utf8_decode($this->createErrorMessage()))] );
        });
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



    private function addError($post, \ReflectionMethod $method)
    {
        $message        = '%s a la même url que la méthode "::%s" de la classe "%s"';
        $this->errors[] = sprintf($message, $post, $method->getName(), $method->getDeclaringClass()->getName());
    }

    private function matchResponseReturn(\ReflectionMethod $method): bool
    {
        return $method->getReturnType() ? ($method->getReturnType()->getName() === 'Symfony\Component\HttpFoundation\Response') : false;
    }

    /**
     * Returns all Urls existing in wordpress
     */
    private function getAllWordpressUrl(): void
    {
        $args = ['post_type' => ['post', 'page'], 'posts_per_page' => -1 ];
        $urls = [];
        $results = new \WP_Query($args);
        if ($results->have_posts()) {
            while ($results->have_posts()) {
                $results->the_post();
                global $post;
                $urls[get_permalink($post->ID)] = $post->post_type . ' "' . $post->post_title . '" (' . $post->ID . ')';
            }
        }
        wp_reset_query();
        $this->wordpressUrls = $urls;
    }


    /**
     * @param \ReflectionMethod $method
     * @throws \ReflectionException
     */
    private function addRouteFromMethod(\ReflectionMethod $method): void
    {

        $class = $method->getDeclaringClass();
        if ($this->hasValidRoute($method)) {
            if(!$this->matchResponseReturn($method)) {
                throw new \Exception(
                    sprintf(
                        'La méthode %s du contôleur %s ne renvoie pas une réponse valide',
                        $method->getName(),
                        $method->getDeclaringClass()->getName()
                    )
                );
            }
            $route     = $this->getRoute($method);
            $arguments = $this->getMethodArguments($method);
            $arguments['_controller'] = $class->getName() . '::' . $method->getName();
            /**
             * check if route exits in wordpress as page or post
             */
            $routePathWithoutVariables = explode('/{', $route->getPath())[0];
            if (substr($routePathWithoutVariables, 0, 1) !== '/') {
                $routePathWithoutVariables = '/' . $routePathWithoutVariables;
            }

            $this->routesPaths[get_bloginfo('url') . $routePathWithoutVariables . '/'] = $method;
            $this->routes->add($route->getName(), new Route($route->getPath(), $arguments));
        }
    }

    /**
     * @param \ReflectionMethod $method
     * @return array
     * @throws \ReflectionException
     */
    private function getMethodArguments(\ReflectionMethod $method): array
    {
        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $arguments[$parameter->getName()] = ($parameter->isOptional()) ? $parameter->getDefaultValue() : null;
        }
        return $arguments;
    }

    /**
     * @param \ReflectionMethod $method
     * @return bool
     */
    private function hasValidRoute(\ReflectionMethod $method): bool
    {
        return null !== $this->reader->getMethodAnnotation($method, self::ROUTE_CLASS);
    }

    /**
     * @param \ReflectionMethod $method
     * @return mixed|object|null
     */
    private function getRoute(\ReflectionMethod $method): ?SiteRoute
    {
        return $this->reader->getMethodAnnotation($method, self::ROUTE_CLASS);
    }

}