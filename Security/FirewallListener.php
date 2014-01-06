<?php

namespace M6Web\Bundle\DomainUserBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class FirewallListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class FirewallListener implements ListenerInterface
{
    protected $securityContext;
    protected $authenticationManager;
    protected $requestContext;
    protected $defaultUsername;
    protected $routerParameter;

    /**
     * Constructor
     *
     * @param SecurityContextInterface       $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     * @param RequestContext                 $requestContext
     * @param string                         $defaultUsername
     * @param string                         $routerParameter
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        RequestContext $requestContext,
        $defaultUsername,
        $routerParameter)
    {
        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->requestContext        = $requestContext;
        $this->defaultUsername       = $defaultUsername;
        $this->routerParameter       = $routerParameter;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function handle(GetResponseEvent $event)
    {
        $request  = $event->getRequest();
        $username = $this->defaultUsername;
        $params   = $request->attributes->get('_route_params');

        if (isset($params[$this->routerParameter])) {
            $this->requestContext->setParameter($this->routerParameter, $params[$this->routerParameter]);
            if (!empty($params[$this->routerParameter])) {
                $username = trim($params[$this->routerParameter], '.');
            }
        }

        $token = new Token($username);
        try {
            $authenticatedToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authenticatedToken);
        } catch (AuthenticationException $e) {
            throw new AccessDeniedException($e->getMessage(), $e);
        }
    }
}
