<?php
namespace Untitled\Event;

use FastRoute\Dispatcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NotFoundEvent
 *
 * @package Untitled\Event
 */
class NotFoundEvent extends RequestAwareEvent
{
    const EVENT_NAME = 'untitled.not_found';

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * NotFoundEvent constructor.
     *
     * @param Request    $request
     * @param Dispatcher $dispatcher
     */
    public function __construct(Request $request, Dispatcher $dispatcher)
    {
        parent::__construct($request);

        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher() : Dispatcher
    {
        return $this->dispatcher;
    }
}
