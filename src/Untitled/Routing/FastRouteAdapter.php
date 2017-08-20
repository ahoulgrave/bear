<?php
namespace Untitled\Routing;

use FastRoute;

/**
 * Class FastRouteAdapter
 * todo: decouple this into a separate composer package
 *
 * @package Untitled\Routing
 */
class FastRouteAdapter extends AbstractRoutingAdapter
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @param array|string[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function resolve(string $uri, string $method): RoutingResolution
    {
        $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r): void {
            $addRoute = function (array $route) use ($r, &$addRoute): void {
                if (is_array($route[1])) {
                    $r->addGroup($route[0], function (FastRoute\RouteCollector $routeCollector) use ($addRoute, $route) {
                        foreach ($route[1] as $subRoute) {
                            $addRoute($subRoute);
                        }
                    });
                } else {
                    list($method, $path, $handler) = $route;
                    $r->addRoute($method, $path, $handler);
                }
            };

            foreach ($this->routes as $route) {
                $addRoute($route);
            }
        });

        $routeInfo = $dispatcher->dispatch($method, $uri);
        $routingResolution = new RoutingResolution();

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $routingResolution->setCode(RoutingResolution::METHOD_NOT_ALLOWED);
                break;
            case FastRoute\Dispatcher::NOT_FOUND:
                $routingResolution->setCode(RoutingResolution::NOT_FOUND);
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $routingResolution
                    ->setCode(RoutingResolution::FOUND)
                    ->setController($handler[0])
                    ->setAction($handler[1])
                    ->setVars($routeInfo[2]);
                break;
        }

        return $routingResolution;
    }
}
