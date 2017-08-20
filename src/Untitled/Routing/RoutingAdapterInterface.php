<?php
namespace Untitled\Routing;

/**
 * Interface Untitled\Routing\RoutingAdapterInterface
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
}
