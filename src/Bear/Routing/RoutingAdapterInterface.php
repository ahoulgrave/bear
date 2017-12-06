<?php
namespace Bear\Routing;

use Psr\Container\ContainerInterface;
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
     * @return Request
     */
    public function getRequest(): Request;

    /**
     * @return void
     */
    public function init(): void;
}
