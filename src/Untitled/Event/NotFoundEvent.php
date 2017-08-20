<?php
namespace Untitled\Event;

use Symfony\Component\HttpFoundation\Request;
use Untitled\Routing\RoutingAdapterInterface;

/**
 * Class NotFoundEvent
 *
 * @package Untitled\Event
 */
class NotFoundEvent extends RequestAwareEvent
{
    const EVENT_NAME = 'untitled.not_found';

    /**
     * @var RoutingAdapterInterface
     */
    private $adapter;

    /**
     * NotFoundEvent constructor.
     *
     * @param Request                 $request
     * @param RoutingAdapterInterface $adapter
     */
    public function __construct(Request $request, RoutingAdapterInterface $adapter)
    {
        parent::__construct($request);

        $this->adapter = $adapter;
    }

    /**
     * @return RoutingAdapterInterface
     */
    public function getAdapter() : RoutingAdapterInterface
    {
        return $this->adapter;
    }
}
