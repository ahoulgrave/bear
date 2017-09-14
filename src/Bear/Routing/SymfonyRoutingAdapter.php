<?php
namespace Bear\Routing;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class SymfonyRoutingAdapter
 * todo: decouple this into a separate composer package
 *
 * @package Bear\Routing
 */
class SymfonyRoutingAdapter extends AbstractRoutingAdapter
{
    /**
     * @var RouteCollection
     */
    private $routeCollection;

    /**
     * @param RouteCollection $routeCollection
     */
    public function __construct(RouteCollection $routeCollection)
    {
        $this->routeCollection = $routeCollection;
    }

    public function resolve(string $uri, string $method): RoutingResolution
    {
        $context = new RequestContext();
        $context->fromRequest($this->request);

        $matcher = new UrlMatcher($this->routeCollection, $context);

        $routingResolution = new RoutingResolution();

        try {
            $info = $matcher->match($uri);
            if (!$info['_controller'] ?? null) {
                throw new \Exception('No controller defined. Please set the "_controller" route parameter.');
            }

            $routingResolution->setCode(RoutingResolution::FOUND);
            $routingResolution->setVars($info);
            $routingResolution->setController((string) $info['_controller']);
            // todo: allow _controller=MyController::action as well and make _action optional
            $routingResolution->setAction((string) $info['_action']);
        } catch (ResourceNotFoundException $e) {
            $routingResolution->setCode(RoutingResolution::NOT_FOUND);
        }

        return $routingResolution;
    }
}
