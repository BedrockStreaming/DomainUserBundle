<?php

namespace M6Web\Bundle\DomainUserBundle\Tests\Units\Security;

use M6Web\Bundle\DomainUserBundle\Security\Token;
use mageekguy\atoum\test;

use M6Web\Bundle\DomainUserBundle\Security\FirewallListener as TestedClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class FirewallListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class FirewallListener extends test
{
    public function testClass()
    {
        $this->testedClass->hasInterface('Symfony\Component\Security\Http\Firewall\ListenerInterface');
    }

    public function testHandleWithClient()
    {
        $tokenStorage = new \mock\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface();
        $authManager  = new \mock\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface();
        $authManager->getMockController()->authenticate = function (TokenInterface $token) {
            if (in_array($token->getUsername(), ['android', 'default'])) {
                return new Token($token->getUsername(), ['ROLE_USER']);
            }
            throw new AuthenticationException();
        };
        $requestContext  = new \mock\Symfony\Component\Routing\RequestContext();

        $listener = new TestedClass($tokenStorage, $authManager, $requestContext, 'default', 'client');

        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], ['_route_params' => ['client' => 'android.']], [], [], []);

        $listener->handle($event);

        $this
            ->mock($tokenStorage)
                ->call('setToken')
                    ->withArguments(new Token('android', ['ROLE_USER']))
                    ->once()
            ->mock($requestContext)
                ->call('setParameter')
                    ->withArguments('client', 'android.')
                    ->once();
    }

    public function testHandleWithDefault()
    {
        $tokenStorage = new \mock\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface();
        $authManager     = new \mock\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface();
        $authManager->getMockController()->authenticate = function (TokenInterface $token) {
            if (in_array($token->getUsername(), ['android', 'default'])) {
                return new Token($token->getUsername(), ['ROLE_USER']);
            }
            throw new AuthenticationException();
        };
        $requestContext  = new \mock\Symfony\Component\Routing\RequestContext();

        $listener = new TestedClass($tokenStorage, $authManager, $requestContext, 'default', 'client');

        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], ['_route_params' => ['client' => '']], [], [], []);

        $listener->handle($event);

        $this
            ->mock($tokenStorage)
                ->call('setToken')
                    ->withArguments(new Token('default', ['ROLE_USER']))
                    ->once()
            ->mock($requestContext)
                ->call('setParameter')
                    ->withArguments('client', '')
                    ->once();
    }

    public function testHandleWithoutParam()
    {
        $tokenStorage = new \mock\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface();
        $authManager     = new \mock\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface();
        $authManager->getMockController()->authenticate = function (TokenInterface $token) {
            if (in_array($token->getUsername(), ['android', 'default'])) {
                return new Token($token->getUsername(), ['ROLE_USER']);
            }
            throw new AuthenticationException();
        };
        $requestContext  = new \mock\Symfony\Component\Routing\RequestContext();

        $listener = new TestedClass($tokenStorage, $authManager, $requestContext, 'default', 'client');

        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], ['_route_params' => []], [], [], []);

        $listener->handle($event);

        $this
            ->mock($tokenStorage)
                ->call('setToken')
                    ->withArguments(new Token('default', ['ROLE_USER']))
                    ->once()
            ->mock($requestContext)
                ->call('setParameter')
                    ->never();
    }

    public function testHandleWithBadClient()
    {
        $tokenStorage = new \mock\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface();
        $authManager     = new \mock\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface();
        $authManager->getMockController()->authenticate = function (TokenInterface $token) {
            if (in_array($token->getUsername(), ['android', 'default'])) {
                return new Token($token->getUsername(), ['ROLE_USER']);
            }
            throw new AuthenticationException();
        };
        $requestContext  = new \mock\Symfony\Component\Routing\RequestContext();

        $listener = new TestedClass($tokenStorage, $authManager, $requestContext, 'default', 'client');

        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], ['_route_params' => ['client' => 'bad.']], [], [], []);

        $this
            ->exception(function () use ($listener, $event) { $listener->handle($event); })
                ->isInstanceOf('Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException')
            ->mock($tokenStorage)
                ->call('setToken')
                    ->never();
    }
}
