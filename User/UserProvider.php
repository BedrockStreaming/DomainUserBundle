<?php

namespace M6Web\Bundle\DomainUserBundle\User;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Yaml\Parser as YamlParser;


/**
 * Class UserProvider
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class UserProvider implements UserProviderInterface
{
    protected $yamlParser;
    protected $usersDir;

    /**
     * Constructor
     *
     * @param YamlParser $yamlParser
     * @param string     $usersDir
     */
    public function __construct(YamlParser $yamlParser, $usersDir)
    {
        $this->yamlParser = $yamlParser;
        $this->usersDir   = $usersDir;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $file = sprintf('%s/%s.yml', $this->usersDir, $username);
        if (!file_exists($file)) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found', $username));
        }
        $userConfig = $this->yamlParser->parse(file_get_contents($file));

        $processor              = new Processor();
        $processedConfiguration = $processor->processConfiguration(new UserConfiguration(), [$userConfig]);

        return new User($username, $processedConfiguration);
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'M6Web\Bundle\DomainUserBundle\User\User';
    }
}
