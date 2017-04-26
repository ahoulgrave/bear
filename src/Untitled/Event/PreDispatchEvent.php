<?php
namespace Untitled\Event;

use Symfony\Component\HttpFoundation\Request;

class PreDispatchEvent extends UntitledEvent
{
    const EVENT_NAME = 'untitled.predispatch';

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
