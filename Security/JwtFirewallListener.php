<?php

namespace M6Web\Bundle\DomainUserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class FirewallListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class JwtFirewallListener extends BaseFirewallListener
{
    protected $key;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        $defaultUsername,
        $key
    ) {
        parent::__construct($securityContext, $authenticationManager, $defaultUsername);

        $this->key = $key;
    }


    /**
     * {@inheritDoc}
     */
    public function getUsernameFromRequest(Request $request)
    {
        if ($request->headers->has('Authorization')) {
            $auth = explode(' ', $request->headers->get('Authorization'));
            try {
                $payload = \JWT::decode($auth[1], $this->key);
                if (!isset($payload->sub)) {
                    throw new \Exception('No username in the token');
                }

                return $payload->sub;
            } catch (\Exception $e) {
                $this->securityContext->setToken(new Token($this->defaultUsername));
                throw new AccessDeniedException($e->getMessage());
            }
        }

        return $this->defaultUsername;
    }
}
