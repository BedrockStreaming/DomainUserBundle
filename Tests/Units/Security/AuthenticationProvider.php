<?php

namespace M6Web\Bundle\DomainUserBundle\Tests\Units\Security;

use M6Web\Bundle\DomainUserBundle\Security\Token;
use mageekguy\atoum\test;

use M6Web\Bundle\DomainUserBundle\Security\AuthenticationProvider as TestedClass;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class AuthenticationProvider
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class AuthenticationProvider extends test
{
    public function testClass()
    {
        $this->testedClass->hasInterface('Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface');
    }

    public function testAuthenticate()
    {
        $user1 = new \M6Web\Bundle\DomainUserBundle\User\User('user1', ['firewall' => ['user_access' => ['default_state' => true]]]);

        $userProvider = new \mock\Symfony\Component\Security\Core\User\UserProviderInterface();
        $userProvider->getMockController()->loadUserByUsername = function ($username) use ($user1) {
            if ($username == 'user1') {
                return $user1;
            } elseif ($username == 'user2') {
                return new \M6Web\Bundle\DomainUserBundle\User\User('user2', ['firewall' => ['user_access' => ['default_state' => false]]]);
            }
            throw new UsernameNotFoundException();
        };

        $this->getMockGenerator()->orphanize('__construct');
        $firewallProvider = new \mock\M6Web\Bundle\FirewallBundle\Firewall\Provider();
        $firewallProvider->getMockController()->getFirewall = function ($name, $config) {
            $firewall = new \mock\M6Web\Bundle\FirewallBundle\Firewall();
            $firewall->getMockController()->handle = $config['default_state'];
            return $firewall;
        };

        $provider = new TestedClass($userProvider, $firewallProvider);

        $this
            ->object($provider->authenticate(new Token('user1')))
                ->isEqualTo(new Token($user1, ['ROLE_USER1']))
            ->exception(function () use ($provider) {$provider->authenticate(new Token('user2'));})
                ->isInstanceOf('Symfony\Component\Security\Core\Exception\AuthenticationException')
            ->exception(function () use ($provider) {$provider->authenticate(new Token('baduser'));})
                ->isInstanceOf('Symfony\Component\Security\Core\Exception\AuthenticationException');
    }
}
