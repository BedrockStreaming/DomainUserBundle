<?php

namespace M6Web\Bundle\DomainUserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class BaseFirewallListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
abstract class BaseFirewallListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $defaultUsername;

    /**
     * Constructor
     *
     * @param SecurityContextInterface       $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     * @param string                         $defaultUsername
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        $defaultUsername)
    {
        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->defaultUsername       = $defaultUsername;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function handle(GetResponseEvent $event)
    {
        try {
            $username = $this->getUsernameFromRequest($event->getRequest());
            $token    = new Token($username);

            $authenticatedToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authenticatedToken);
        } catch (AuthenticationException $e) {
            throw new AccessDeniedException($e->getMessage(), $e);
        }
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    abstract public function getUsernameFromRequest(Request $request);
}
