<?php
namespace Bear\Event;

use Symfony\Component\HttpFoundation\Request;
use Bear\Routing\RoutingAdapterInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class NotFoundEvent
 *
 * @package Bear\Event
 */
class NotFoundEvent extends RequestAwareEvent
{
    const EVENT_NAME = 'bear.not_found';

    /**
     * @var RoutingAdapterInterface
     */
    private $adapter;

    /**
     * @var Response|null
     */
    private $response;

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

    /**
     * @return null|Response
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param null|Response $response
     *
     * @return void
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }
}
