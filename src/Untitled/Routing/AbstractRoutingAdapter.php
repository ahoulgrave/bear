<?php
namespace Untitled\Routing;

use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @param Request $request
     *
     * @return void
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
}
