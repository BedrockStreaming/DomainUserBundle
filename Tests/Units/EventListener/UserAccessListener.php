<?php

namespace M6Web\Bundle\DomainUserBundle\Tests\Units\EventListener;

use M6Web\Bundle\DomainUserBundle\EventListener\UserAccessListener as TestedClass;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use mageekguy\atoum\test;
use Symfony\Component\HttpFoundation\Request;

/**
 * UserAccessListener test
 */
class UserAccessListener extends test
{
    /**
     * @dataProvider userAccessGrantedDataProvider
     */
    public function testUserAccessGranted(array $configFirewallAllow, array $requestAttribute)
    {
        // user mock
        $this->getMockGenerator()->orphanize('__construct');
        $userMock = new \mock\Symfony\Component\Security\Core\User();
        $userMock->getMockController()->getConfigFirewallAllow = $configFirewallAllow;

        // token mock
        $this->getMockGenerator()->orphanize('__construct');
        $tokenMock = new \mock\M6Web\Bundle\DomainUserBundle\Security\Token();
        $tokenMock->getMockController()->getUser = $userMock;

        // tokenstorage mock
        $this->getMockGenerator()->orphanize('__construct');
        $tokenStorage = new \mock\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface();
        $tokenStorage->getMockController()->getToken = $tokenMock;

        // event mock
        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], $requestAttribute);
        $event->getMockController()->getRequestType = HttpKernelInterface::MASTER_REQUEST;

        $this
            ->given($listener = new TestedClass($tokenStorage, false))
            ->if($listener->onKernelRequest($event))
            ->then() // juste to see if no exception has been thrown
                ->boolean(true)->isTrue();
    }

    public function userAccessGrantedDataProvider()
    {
        return [
            // granted by default
            [
                [
                    'default'=>true
                ],
                []
            ],
            // granted by the method
            [
                [
                    'default' => false,
                    'methods' => ['get' => true]
                ],
                []
            ],
            // granted on a particular ressource
            [
                [
                    'default'   => false,
                    'methods'   => ['get' => false],
                    'resources' => ['clip' => true]
                ],
                [
                    '_route_params' => ['_resource' => 'clip']
                ]
            ],
            // granted on a particular route
            [
                [
                    'default'   => false,
                    'methods'   => ['get' => false],
                    'resources' => ['clip' => false],
                    'routes'    => ['paradise' => true]
                ],
                [
                    '_route_params' => ['_resource' => 'clip'],
                    '_route'        => 'paradise'
                ]
            ],
            // granted on profiler route
            [
                [
                    'default'           => false,
                    'methods'           => ['get' => false],
                    'resources'         => ['clip' => false],
                    'routes'            => ['_profiler' => false],
                    'allow_debug_route' => true
                ],
                [
                    '_route_params' => ['_resource' => 'clip'],
                    '_route'        => '_profiler'
                ]
            ],
        ];
    }

    /**
     * @dataProvider userAccessDeniedDataProvider
     */
    public function testUserAccessDenied(array $configFirewallAllow, array $requestAttribute)
    {
        // user mock
        $this->getMockGenerator()->orphanize('__construct');
        $userMock = new \mock\Symfony\Component\Security\Core\User();
        $userMock->getMockController()->getConfigFirewallAllow = $configFirewallAllow;

        // token mock
        $this->getMockGenerator()->orphanize('__construct');
        $tokenMock = new \mock\M6Web\Bundle\DomainUserBundle\Security\Token();
        $tokenMock->getMockController()->getUser = $userMock;

        // tokenstorage mock
        $this->getMockGenerator()->orphanize('__construct');
        $tokenStorage = new \mock\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface();
        $tokenStorage->getMockController()->getToken = $tokenMock;

        // event mock
        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], $requestAttribute);
        $event->getMockController()->getRequestType = HttpKernelInterface::MASTER_REQUEST;

        $this
            ->given($listener = new TestedClass($tokenStorage, true))
            ->exception(
                function() use($listener, $event) {
                    $listener->onKernelRequest($event);
                }
            )
            ->isInstanceOf('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException');
    }

    public function userAccessDeniedDataProvider()
    {
        return [
            // denied by default
            [
                [
                    'default'=>false
                ],
                []
            ],
            // denied by the method
            [
                [
                    'default' => true,
                    'methods' => ['get' => false]
                ],
                []
            ],
            // denied on a particular ressource
            [
                [
                    'default'   => true,
                    'methods'   => ['get' => true],
                    'resources' => ['clip' => false]
                ],
                [
                    '_route_params' => ['_resource' => 'clip']
                ]
            ],
            // denied on a particular route
            [
                [
                    'default'   => true,
                    'methods'   => ['get' => true],
                    'resources' => ['clip' => true],
                    'routes'    => ['paradise' => false]
                ],
                [
                    '_route_params' => ['_resource' => 'clip'],
                    '_route'        => 'paradise'
                ]
            ],
        ];
    }
}
