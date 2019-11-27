<?php

namespace M6Web\Bundle\DomainUserBundle\Tests\Units\User;

use M6Web\Bundle\DomainUserBundle\User\User;
use M6Web\Bundle\DomainUserBundle\User\UserConfiguration as UserConf;
use mageekguy\atoum\test;

use M6Web\Bundle\DomainUserBundle\User\UserProvider as TestedClass;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class UserProvider
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class UserProvider extends test
{
    public function testClass()
    {
        $this->testedClass->hasInterface('Symfony\Component\Security\Core\User\UserProviderInterface');
    }

    public function testLoadUserByUsername()
    {
        $parser   = new \Symfony\Component\Yaml\Parser();
        $provider = new TestedClass($parser, __DIR__.'/../../Fixtures/users/', '/tmp');

        $processor = new Processor();

        $this
            ->object($user = $provider->getUserByUserName('user1'))
                ->isInstanceOf('M6Web\Bundle\DomainUserBundle\User\User')
                ->isEqualTo(new User('user1', $processor->processConfiguration(new UserConf(), [[]])))
            ->object($user = $provider->getUserByUserName('user2'))
                ->isInstanceOf('M6Web\Bundle\DomainUserBundle\User\User')
                ->isEqualTo(new User('user2', $processor->processConfiguration(new UserConf(), [$parser->parse(file_get_contents(__DIR__.'/../../Fixtures/users/user2.yml'))])))
            ->object($user = $provider->getUserByUserName('user3'))
                ->isInstanceOf('M6Web\Bundle\DomainUserBundle\User\User')
                ->isEqualTo(new User('user3', $processor->processConfiguration(new UserConf(), [$parser->parse(file_get_contents(__DIR__.'/../../Fixtures/users/user3.yaml'))])))
            ->exception(function () use ($provider) { $provider->getUserByUserName('unknownuser'); })
                ->isInstanceOf('Symfony\Component\Security\Core\Exception\UsernameNotFoundException');
    }
}
