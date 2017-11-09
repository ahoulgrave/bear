<?php
namespace Bear\Tests;

use Bear\App;
use Bear\Routing\AbstractRoutingAdapter;
use Bear\Routing\RoutingResolution;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Zend\ServiceManager\ServiceManager;

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
        $config = [
            'serviceManager' => [true],
            'routing'        => $routingAdapterMock,
        ];

        $serviceManagerMock = $this
            ->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $serviceManagerMock->method('get')->willReturn(new class {
            public function testAction(): Response
            {
                return new Response('test response');
            }
        });

        $appMock = $this
            ->getMockBuilder(App::class)
            ->setConstructorArgs([$config])
            ->setMethodsExcept(['run'])
            ->getMock();

        $appReflection = new \ReflectionClass(App::class);

        $serviceManagerProperty = $appReflection->getProperty('serviceManager');
        $serviceManagerProperty->setAccessible(true);
        $serviceManagerProperty->setValue($appMock, $serviceManagerMock);
        $serviceManagerProperty->setAccessible(false);

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
        $config = [
            'serviceManager' => [true],
            'routing'        => $routingAdapterMock,
        ];

        $serviceManagerMock = $this
            ->getMockBuilder(ServiceManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $serviceManagerMock->method('get')->willReturn(new class {
            public function testAction(): Response
            {
                return new Response('test response');
            }
        });

        $appMock = $this
            ->getMockBuilder(App::class)
            ->setConstructorArgs([$config])
            ->setMethodsExcept(['run'])
            ->getMock();

        $appReflection = new \ReflectionClass(App::class);

        $serviceManagerProperty = $appReflection->getProperty('serviceManager');
        $serviceManagerProperty->setAccessible(true);
        $serviceManagerProperty->setValue($appMock, $serviceManagerMock);
        $serviceManagerProperty->setAccessible(false);

        ob_start();
        $appMock->run();

        $this->assertEquals('Method not found', ob_get_clean());
    }
}
