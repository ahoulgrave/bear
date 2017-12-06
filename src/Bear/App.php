<?php
namespace Bear;

use Bear\Event\ControllerResolutionEvent;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Bear\Event\NotFoundEvent;
use Bear\Event\PostDispatchEvent;
use Bear\Event\PreResolveEvent;
use Bear\Routing\AbstractRoutingAdapter;
use Bear\Routing\RoutingAdapterInterface;
use Bear\Routing\RoutingResolution;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class App
 */
class App
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var RoutingAdapterInterface
     */
    private $routingAdapter;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param ContainerInterface          $container                 A valid PSR-11 container instance
     * @param string                      $routingAdapterContainerId A valid container identifier returning a RoutingAdapterInterface instance
     * @param string|EventDispatcher|null $eventDispatcher           Optional. An EventDispatcher instance or a valid container identifier returning an EventDispatcher instance
     *
     * @throws \Exception
     */
    public function __construct(ContainerInterface $container, string $routingAdapterContainerId, $eventDispatcher = null)
    {
        $this->container       = $container;

        if (!$this->container->has($routingAdapterContainerId)) {
            throw new \Exception('You need to provide a routing container identifier.');
        }

        $routingAdapter = $this->container->get($routingAdapterContainerId);

        if (!$routingAdapter instanceof RoutingAdapterInterface) {
            throw new \Exception(sprintf('The routing provider identifier must return an object implementing %s interface.', RoutingAdapterInterface::class));
        }

        $this->request = $routingAdapter->getRequest();

        if ($eventDispatcher instanceof EventDispatcher) {
            $this->eventDispatcher = $eventDispatcher;
        } elseif (is_string($eventDispatcher) && $this->container->has($eventDispatcher)) {
            $this->eventDispatcher = $this->container->get($eventDispatcher);
        } else {
            $this->eventDispatcher = new EventDispatcher();
        }
    }

    /**
     * @return Response
     */
    protected function resolveRequest(): Response
    {
        $httpMethod = $this->request->getMethod();
        $uri = $this->request->getPathInfo();

        $routingAdapter = $this->routingAdapter;

        // move init to constructor
        $routingAdapter->init();
        $routingAdapter->registerService($this->container);

        $routingResolution = $routingAdapter->resolve($uri, $httpMethod);
        switch ($routingResolution->getCode()) {
            case RoutingResolution::NOT_FOUND:
                $response = new Response('Not found', Response::HTTP_NOT_FOUND);
                // Dispatch event
                $notFoundEvent = new NotFoundEvent($this->request, $routingAdapter);
                $this->eventDispatcher->dispatch(NotFoundEvent::EVENT_NAME, $notFoundEvent);

                if ($eventResponse = $notFoundEvent->getResponse()) {
                    return $eventResponse;
                }

                return $response;
            case RoutingResolution::FOUND:
                $vars = $routingResolution->getVars();
                $this->request->attributes->add($vars);
                $controller = $routingResolution->getController();
                $controllerInstance = $this->container->get($controller);
                $this->eventDispatcher->dispatch(ControllerResolutionEvent::EVENT_NAME, new ControllerResolutionEvent($this->request, $controllerInstance));

                $action = $routingResolution->getAction();

                /** @var Response $response */
                if (method_exists($controllerInstance, $action)) {
                    $response = $controllerInstance->{$action}($this->request);
                } elseif (method_exists($controllerInstance, $actionMethod = sprintf('%sAction', $action))) {
                    $response = $controllerInstance->{$actionMethod}($this->request);
                } else {
                    $response = new Response('Method not found', Response::HTTP_NOT_FOUND);
                }

                // Fire postdispatch event
                $postDispatchEvent = new PostDispatchEvent($this->request, $response);
                $this->eventDispatcher->dispatch(PostDispatchEvent::EVENT_NAME, $postDispatchEvent);

                return $response;
            default:
                return new Response(sprintf('Invalid resolution code: %s', $routingResolution->getCode()), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->eventDispatcher->dispatch(PreResolveEvent::EVENT_NAME, new PreResolveEvent());

        $response = $this->resolveRequest();

        $response->send();
    }
}
