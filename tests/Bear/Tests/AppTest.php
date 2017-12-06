<?php
namespace Bear\Tests;

use Bear\App;
use Bear\Routing\AbstractRoutingAdapter;
use Bear\Routing\RoutingResolution;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AppTest
 *
 * @package Bear\Tests
 */
class AppTest extends TestCase
{
    /**
     * @return void
     */
    public function testRun(): void
    {
        $routingAdapterMock = $this->getMockForAbstractClass(AbstractRoutingAdapter::class);
        $routingResolution  = new RoutingResolution();
        $routingResolution->setCode(RoutingResolution::FOUND);
        $routingResolution->setAction('test');

        $routingAdapterMock->method('resolve')->willReturn($routingResolution);

        $appMock = $this
            ->getMockBuilder(App::class)
            ->setConstructorArgs([new class($routingAdapterMock) implements ContainerInterface {
                private $routingAdapterMock;

                public function __construct($routingAdapterMock)
                {
                    $this->routingAdapterMock = $routingAdapterMock;
                }

                public function get($id)
                {
                    if ($id === 'routingAdapter') {
                        return $this->routingAdapterMock;
                    }

                    return new class {
                        public function testAction(): Response
                        {
                            return new Response('test response');
                        }
                    };
                }

                public function has($id)
                {
                    if ($id === 'routingAdapter') {
                        return true;
                    }

                    return false;
                }
            }, 'routingAdapter'])
            ->setMethodsExcept(['run'])
            ->getMock();

        ob_start();
        $appMock->run();

        $this->assertEquals('test response', ob_get_clean());
    }

    /**
     * @return void
     */
    public function testRunWithNonExistingAction(): void
    {
        $routingAdapterMock = $this->getMockForAbstractClass(AbstractRoutingAdapter::class);
        $routingResolution  = new RoutingResolution();
        $routingResolution->setCode(RoutingResolution::FOUND);
        $routingResolution->setAction('fakeTest');

        $routingAdapterMock->method('resolve')->willReturn($routingResolution);

        $appMock = $this
            ->getMockBuilder(App::class)
            ->setConstructorArgs([new class($routingAdapterMock) implements ContainerInterface {
                private $routingAdapterMock;

                public function __construct($routingAdapterMock)
                {
                    $this->routingAdapterMock = $routingAdapterMock;
                }

                public function get($id)
                {
                    if ($id === 'routingAdapter') {
                        return $this->routingAdapterMock;
                    }

                    return new class {
                        public function testAction(): Response
                        {
                            return new Response('test response');
                        }
                    };
                }

                public function has($id)
                {
                    if ($id === 'routingAdapter') {
                        return true;
                    }

                    return false;
                }
            }, 'routingAdapter'])
            ->setMethodsExcept(['run'])
            ->getMock();

        ob_start();
        $appMock->run();

        $this->assertEquals('Method not found', ob_get_clean());
    }
}
