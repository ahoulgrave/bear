<?php
namespace Bear\Tests\Event;

use Bear\Event\NotFoundEvent;
use Bear\Routing\AbstractRoutingAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class NotFoundEventTest
 *
 * @package Bear\Tests\Event
 */
class NotFoundEventTest extends TestCase
{
    /**
     * @return void
     */
    public function testSimpleEvent(): void
    {
        $adapterMock = $this->getMockForAbstractClass(AbstractRoutingAdapter::class);
        $request = new Request();
        $request->query->add(['testVar' => 1]);
        $event = new NotFoundEvent($request, $adapterMock);

        $this->assertEquals(1, $event->getRequest()->get('testVar'));
    }
}
