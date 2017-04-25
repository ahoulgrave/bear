<?php
namespace Untitled;

use FastRoute\Dispatcher;
use Zend\ServiceManager\ServiceManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use zpt\anno\Annotations;

/**
 * Class App
 */
class App
{
    /**
     * @param array $config
     */
    public static function init(array $config)
    {
        $request = Request::createFromGlobals();

        $serviceManager = new ServiceManager($config['service_manager']);

        $httpMethod = $request->getMethod();
        $uri = $request->getPathInfo();

        $dispatcher = $config['routes'];

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                // ... 404 Not Found
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                // ... 405 Method Not Allowed
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $request->attributes->add($vars);
                $controller = $handler[0];
                $controllerInstance = $serviceManager->get($controller);
                $controllerReflection = new \ReflectionClass($controller);

                foreach ($controllerReflection->getProperties() as $property) {
                    $annotation = new Annotations($property);
                    if ($dependencyIdentifier = $annotation['inject'] ?? null) {
                        if (class_exists($dependencyIdentifier)) {
                            $dependencyIdentifier = $dependencyReflection = (new \ReflectionClass($annotation['inject']))->getName();
                        }

                        $dependency = $serviceManager->get($dependencyIdentifier);
                        $method = $controllerReflection->getMethod(sprintf('set%s', ucfirst($property->getName())))->getName();
                        $controllerInstance->{$method}($dependency);
                    }
                }

                $action = $handler[1];
                /** @var Response $response */
                $response = $controllerInstance->{sprintf('%sAction', $action)}($request);
                $response->send();
                break;
        }
    }
}
