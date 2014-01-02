<?php

namespace M6Web\Bundle\DomainUserBundle\Security;
use M6Web\Bundle\FirewallBundle\Firewall\Provider;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;


/**
 * Class AuthenticationProvider
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class AuthenticationProvider implements AuthenticationProviderInterface
{
    protected $userProvider;
    protected $firewallProvider;

    /**
     * Constructor
     *
     * @param UserProviderInterface $userProvider
     * @param Provider              $firewallProvider
     */
    public function __construct(UserProviderInterface $userProvider, Provider $firewallProvider)
    {
        $this->userProvider     = $userProvider;
        $this->firewallProvider = $firewallProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof Token;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(TokenInterface $token)
    {
        try {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());

            $firewall = $this->firewallProvider->getFirewall(null, $user->getConfigFirewallUserAccess());

            if (!$firewall->handle()) {
                throw new AuthenticationException(sprintf('Access denied for user "%s"', $token->getUsername()));
            }

            $authenticatedToken = new Token($user, $user->getRoles());

            return $authenticatedToken;
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException($e->getMessage());
        }
    }
}
