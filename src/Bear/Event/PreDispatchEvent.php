<?php
namespace Bear\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PreDispatchEvent
 *
 * @package Bear\Event
 */
class PreDispatchEvent extends RequestAwareEvent
{
    const EVENT_NAME = 'brear.predispatch';

    /**
     * @var Response
     */
    private $response;

    /**
     * PreDispatchEvent constructor.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request);
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     *
     * @return void
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
