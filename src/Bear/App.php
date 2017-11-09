<?php
namespace Bear;

use Bear\Event\ControllerResolutionEvent;
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

/**
 * Class App
 */
class App
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

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
     * @param array $config
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config)
    {
        $this->config          = $config;

        if (!$this->config['serviceManager'] ?? null) {
            throw new \InvalidArgumentException('Please provide a "serviceManager" configuration key');
        }

        $this->serviceManager  = new ServiceManager($config['serviceManager']);
        $this->eventDispatcher = $this->buildEventDispatcher();
        $this->routingAdapter  = $this->buildRoutingAdapter();
        $this->request         = Request::createFromGlobals();

        if ($this->routingAdapter instanceof AbstractRoutingAdapter) {
            $this->routingAdapter->setRequest($this->request);
        }
    }

    /**
     * @return EventDispatcher
     */
    protected function buildEventDispatcher(): EventDispatcher
    {
        $serviceManager = $this->serviceManager;
        $eventDispatcher = $this->config['eventDispatcher'] ?? new EventDispatcher();

        if (is_callable($eventDispatcher)) {
            $eventDispatcher = $eventDispatcher($serviceManager);
        } elseif (!is_object($eventDispatcher) && $serviceManager->has($eventDispatcher)) {
            $eventDispatcher = $serviceManager->get($eventDispatcher);
        }

        return $eventDispatcher;
    }

    /**
     * @return RoutingAdapterInterface
     *
     * @throws \Exception
     */
    protected function buildRoutingAdapter(): RoutingAdapterInterface
    {
        $routingAdapter = $this->config['routing'] ?? null;

        if (!$routingAdapter instanceof RoutingAdapterInterface) {
            throw new \Exception('Are you sure you provided the "routing" config value with a RoutingAdapter?');
        }

        return $routingAdapter;
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
        $routingAdapter->registerService($this->serviceManager);

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
                $controllerInstance = $this->serviceManager->get($controller);
                $this->eventDispatcher->dispatch(ControllerResolutionEvent::EVENT_NAME, new ControllerResolutionEvent($this->request, $controllerInstance));

                $action = $routingResolution->getAction();
                $actionMethod = sprintf('%sAction', $action);
                /** @var Response $response */
                if (method_exists($controllerInstance, $actionMethod)) {
                    $response = $controllerInstance->{$actionMethod}($this->request);
                } else {
                    $response = new Response('Method not found', Response::HTTP_NOT_FOUND);
                }

                // Fire predispatch event
                // todo: add response to the event
                $preDispatchEvent = new PreDispatchEvent($this->request);
                $this->eventDispatcher->dispatch(PreDispatchEvent::EVENT_NAME, $preDispatchEvent);

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
        // Handle routing
        $response = $this->resolveRequest();
        // todo: add event
        $response->send();
    }
}
