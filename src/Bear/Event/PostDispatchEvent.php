<?php
namespace Bear\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PostDispatchEvent
 *
 * @package Bear\Event
 */
class PostDispatchEvent extends RequestAwareEvent
{
    const EVENT_NAME = 'bear.postdispatch';

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
     * @codeCoverageIgnore
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
