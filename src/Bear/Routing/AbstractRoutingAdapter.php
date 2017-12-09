<?php
namespace Bear\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractRoutingAdapter
 *
 * @package Bear\Routing
 */
abstract class AbstractRoutingAdapter implements RoutingAdapterInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
