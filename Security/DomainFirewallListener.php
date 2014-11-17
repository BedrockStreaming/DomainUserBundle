<?php

namespace M6Web\Bundle\DomainUserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class FirewallListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class DomainFirewallListener extends BaseFirewallListener
{
    protected $requestContext;
    protected $routerParameter;

    /**
     * Constructor
     *
     * @param SecurityContextInterface       $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     * @param string                         $defaultUsername
     * @param RequestContext                 $requestContext
     * @param string                         $routerParameter
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        $defaultUsername,
        RequestContext $requestContext,
        $routerParameter)
    {
        parent::__construct($securityContext, $authenticationManager, $defaultUsername);

        $this->requestContext        = $requestContext;
        $this->routerParameter       = $routerParameter;
    }

    /**
     * {@inheritDoc}
     */
    public function getUsernameFromRequest(Request $request)
    {
        $username = $this->defaultUsername;
        $params   = $request->attributes->get('_route_params');

        if (isset($params[$this->routerParameter])) {
            $this->requestContext->setParameter($this->routerParameter, $params[$this->routerParameter]);
            if (!empty($params[$this->routerParameter])) {
                $username = trim($params[$this->routerParameter], '.');
            }
        }

        return $username;
    }
}
