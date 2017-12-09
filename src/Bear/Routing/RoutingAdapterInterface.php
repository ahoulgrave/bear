<?php
namespace Bear\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface Bear\Routing\RoutingAdapterInterface
 *
 */
interface RoutingAdapterInterface
{
    /**
     * @param string $uri
     * @param string $method
     *
     * @return RoutingResolution
     */
    public function resolve(string $uri, string $method): RoutingResolution;

    /**
     * @return Request
     */
    public function getRequest(): Request;
}
