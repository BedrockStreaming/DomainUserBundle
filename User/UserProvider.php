<?php

namespace M6Web\Bundle\DomainUserBundle\User;

use M6Web\Bundle\DomainUserBundle\User\User;
use M6Web\Bundle\DomainUserBundle\Cache\Warmup;
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
     * @var integer
     */
    protected $ttl;

    /**
     * @var string
     */
    protected $kernelCacheDir;

    /**
     * Constructor
     *
     * @param YamlParser $yamlParser
     * @param string     $usersDir
     * @param string     $kernelCacheDir
     */
    public function __construct(YamlParser $yamlParser, $usersDir, $kernelCacheDir)
    {
        $this->yamlParser     = $yamlParser;
        $this->usersDir       = $usersDir;
        $this->kernelCacheDir = $kernelCacheDir;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        return require Warmup::getCachePath($this->kernelCacheDir, $username);
    }

    /**
     * process the yaml file a create a user object
     *
     * @param $username
     *
     * @return \M6Web\Bundle\DomainUserBundle\User\User
     */
    public function getUserByUserName($username)
    {
        $file = sprintf('%s/%s.yml', $this->usersDir, $username);
        if (!file_exists($file)) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found', $username));
        }
        $userConfig             = $this->yamlParser->parse(file_get_contents($file)); // parse yaml file
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
