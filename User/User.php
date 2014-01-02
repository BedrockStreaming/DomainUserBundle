<?php

namespace M6Web\Bundle\DomainUserBundle\User;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class User implements UserInterface
{
    protected $username;
    protected $config;

    /**
     * Constructor
     *
     * @param string $username
     * @param string $config
     */
    public function __construct($username, $config)
    {
        $this->username = $username;
        $this->config   = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return ['ROLE_'.strtoupper($this->username)];
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return array
     */
    public function getConfigFirewallUserAccess()
    {
        return $this->config['firewall']['user_access'];
    }

    /**
     * @return array
     */
    public function getConfigFirewallAllow()
    {
        return $this->config['firewall']['allow'];
    }

    /**
     * @return array
     */
    public function getConfigCache()
    {
        return $this->config['cache'];
    }

    public function getConfigEntities()
    {
        return $this->config['entities'];
    }
}
