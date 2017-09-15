<?php
namespace Bear;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Bear\Event\NotFoundEvent;
use Bear\Event\PreDispatchEvent;
use Bear\Event\PreResolveEvent;
use Bear\Routing\AbstractRoutingAdapter;
use Bear\Routing\RoutingAdapterInterface;
use Bear\Routing\RoutingResolution;
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
     *
     * @throws \Exception
     */
    public static function init(array $config): void
    {
        $request = Request::createFromGlobals();

        $serviceManager = new ServiceManager($config['service_manager']);

        // Prepare event dispatcher
        $eventDispatcher = $config['eventDispatcher'] ?? new EventDispatcher();
        if (is_callable($eventDispatcher)) {
            $eventDispatcher = $eventDispatcher($serviceManager);
        } elseif (!is_object($eventDispatcher) && $serviceManager->has($eventDispatcher)) {
            $eventDispatcher = $serviceManager->get($eventDispatcher);
        }

        $eventDispatcher->dispatch(PreResolveEvent::EVENT_NAME, new PreResolveEvent());

        // Handle routing
        $httpMethod = $request->getMethod();
        $uri = $request->getPathInfo();

        $routingAdapter = $config['routing'];

        if (!$routingAdapter instanceof RoutingAdapterInterface) {
            throw new \Exception('Are you sure you provided the "routing" config value with a RoutingAdapter?');
        }

        if ($routingAdapter instanceof AbstractRoutingAdapter) {
            $routingAdapter->setRequest($request);
        }

        $routingAdapter->init();

        $routingResolution = $routingAdapter->resolve($uri, $httpMethod);
        switch ($routingResolution->getCode()) {
            case RoutingResolution::NOT_FOUND:
                $response = new Response('Not found', Response::HTTP_NOT_FOUND);
                // Dispatch event
                // todo: add response to the event
                // todo: remove dispatcher and add route info
                $notFoundEvent = new NotFoundEvent($request, $routingAdapter);
                $eventDispatcher->dispatch(NotFoundEvent::EVENT_NAME, $notFoundEvent);
                $response->send();
                break;
            case RoutingResolution::METHOD_NOT_ALLOWED:
                $allowedMethods = $routingResolution[1];
                // todo: add event with: $request, $response, $routeInfo
                $methodNotAllowedResponse = new Response('Method not allowed', Response::HTTP_METHOD_NOT_ALLOWED);
                $methodNotAllowedResponse->send();
                break;
            case RoutingResolution::FOUND:
                $vars = $routingResolution->getVars();
                $request->attributes->add($vars);
                $controller = $routingResolution->getController();
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

                $action = $routingResolution->getAction();
                $actionMethod = sprintf('%sAction', $action);
                /** @var Response $response */
                if (method_exists($controllerInstance, $actionMethod)) {
                    $response = $controllerInstance->{$actionMethod}($request);
                } else {
                    $response = new Response('Method not found', Response::HTTP_NOT_FOUND);
                }

                // Fire predispatch event
                // todo: add response to the event
                $preDispatchEvent = new PreDispatchEvent($request);
                $eventDispatcher->dispatch(PreDispatchEvent::EVENT_NAME, $preDispatchEvent);

                $response->send();
                break;
        }
    }
}
