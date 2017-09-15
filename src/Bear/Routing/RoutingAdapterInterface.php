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
     * @param Request $request
     *
     * @return void
     */
    public function setRequest(Request $request): void;

    /**
     * @return void
     */
    public function init(): void;
}
