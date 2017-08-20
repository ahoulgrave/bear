<?php
namespace Untitled\Routing;

use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @param Request $request
     *
     * @return void
     */
    public function setRequest(Request $request): void;
}
