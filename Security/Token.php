<?php

namespace M6Web\Bundle\DomainUserBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * Class Token
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class Token extends AbstractToken
{
    /**
     * Constructor
     *
     * @param mixed $username
     * @param array $roles
     */
    public function __construct($username, array $roles = array())
    {
        parent::__construct($roles);
        $this->setUser($username);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return '';
    }
}
