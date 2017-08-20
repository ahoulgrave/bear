<?php
require __DIR__ . '/../vendor/autoload.php';

/**
 * Class TestController
 */
class TestController {
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function testAction(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return new \Symfony\Component\HttpFoundation\Response(sprintf('Hello %s!', $request->get('name', 'Stranger')));
    }
}

$config = [
    'routing' => new \Untitled\Routing\FastRouteAdapter([
        ['/group',[
            ['GET', '/[{name}]', [TestController::class, 'test']],
        ]],
        ['GET', '/[{name}]', [TestController::class, 'test']],
    ]),
    'service_manager' => [
        'services' => [
            TestController::class => new TestController(),
        ],
    ],
];

\Untitled\App::init($config);
