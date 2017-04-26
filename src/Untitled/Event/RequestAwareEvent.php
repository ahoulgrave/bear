<?php
namespace Untitled\Event;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestAwareEvent
 *
 * @package Untitled\Event
 */
abstract class RequestAwareEvent extends UntitledEvent
{
    /**
     * @var Request
     */
    private $request;

    /**
     * PreDispatchEvent constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest() : Request
    {
        return $this->request;
    }
}
