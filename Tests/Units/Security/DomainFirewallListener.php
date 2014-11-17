<?php

namespace M6Web\Bundle\DomainUserBundle\Tests\Units\Security;

use M6Web\Bundle\DomainUserBundle\Security\Token;
use mageekguy\atoum\test;

use M6Web\Bundle\DomainUserBundle\Security\DomainFirewallListener as TestedClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class DomainFirewallListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class DomainFirewallListener extends test
{
    public function testClass()
    {
        $this->testedClass->hasInterface('Symfony\Component\Security\Http\Firewall\ListenerInterface');
    }

    public function testHandleWithClient()
    {
        $securityContext = new \mock\Symfony\Component\Security\Core\SecurityContextInterface();
        $authManager     = new \mock\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface();
        $authManager->getMockController()->authenticate = function (TokenInterface $token) {
            if (in_array($token->getUsername(), ['android', 'default'])) {
                return new Token($token->getUsername(), ['ROLE_USER']);
            }
            throw new AuthenticationException();
        };
        $requestContext  = new \mock\Symfony\Component\Routing\RequestContext();

        $listener = new TestedClass($securityContext, $authManager, 'default', $requestContext, 'client');

        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], ['_route_params' => ['client' => 'android.']], [], [], []);

        $listener->handle($event);

        $this
            ->mock($securityContext)
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
        $securityContext = new \mock\Symfony\Component\Security\Core\SecurityContextInterface();
        $authManager     = new \mock\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface();
        $authManager->getMockController()->authenticate = function (TokenInterface $token) {
            if (in_array($token->getUsername(), ['android', 'default'])) {
                return new Token($token->getUsername(), ['ROLE_USER']);
            }
            throw new AuthenticationException();
        };
        $requestContext  = new \mock\Symfony\Component\Routing\RequestContext();

        $listener = new TestedClass($securityContext, $authManager, 'default', $requestContext, 'client');

        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], ['_route_params' => ['client' => '']], [], [], []);

        $listener->handle($event);

        $this
            ->mock($securityContext)
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
        $securityContext = new \mock\Symfony\Component\Security\Core\SecurityContextInterface();
        $authManager     = new \mock\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface();
        $authManager->getMockController()->authenticate = function (TokenInterface $token) {
            if (in_array($token->getUsername(), ['android', 'default'])) {
                return new Token($token->getUsername(), ['ROLE_USER']);
            }
            throw new AuthenticationException();
        };
        $requestContext  = new \mock\Symfony\Component\Routing\RequestContext();

        $listener = new TestedClass($securityContext, $authManager, 'default', $requestContext, 'client');

        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], ['_route_params' => []], [], [], []);

        $listener->handle($event);

        $this
            ->mock($securityContext)
                ->call('setToken')
                    ->withArguments(new Token('default', ['ROLE_USER']))
                    ->once()
            ->mock($requestContext)
                ->call('setParameter')
                    ->never();
    }

    public function testHandleWithBadClient()
    {
        $securityContext = new \mock\Symfony\Component\Security\Core\SecurityContextInterface();
        $authManager     = new \mock\Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface();
        $authManager->getMockController()->authenticate = function (TokenInterface $token) {
            if (in_array($token->getUsername(), ['android', 'default'])) {
                return new Token($token->getUsername(), ['ROLE_USER']);
            }
            throw new AuthenticationException();
        };
        $requestContext  = new \mock\Symfony\Component\Routing\RequestContext();

        $listener = new TestedClass($securityContext, $authManager, 'default', $requestContext, 'client');

        $this->getMockGenerator()->orphanize('__construct');
        $event = new \mock\Symfony\Component\HttpKernel\Event\GetResponseEvent();
        $event->getMockController()->getRequest = new Request([], [], ['_route_params' => ['client' => 'bad.']], [], [], []);

        $this
            ->exception(function () use ($listener, $event) { $listener->handle($event); })
                ->isInstanceOf('Symfony\Component\Security\Core\Exception\AccessDeniedException')
            ->mock($securityContext)
                ->call('setToken')
                    ->never();
    }
}
