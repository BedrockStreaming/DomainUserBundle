<?php

namespace M6Web\Bundle\DomainUserBundle\EventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class FirewallListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class UserAccessListener
{
    protected $context;

    /**
     * Constructor
     *
     * @param SecurityContextInterface $context
     */
    public function __construct(SecurityContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Check access on request
     *
     * @param GetResponseEvent $event
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }
        $request     = $event->getRequest();
        $user        = $this->context->getToken()->getUser();
        $allowConfig = $user->getConfigFirewallAllow();

        // 0.
        $allowed = $allowConfig['default'];

        // 1. Override by method
        if (isset($allowConfig['methods'][strtolower($request->getMethod())])) {
            $allowed = $allowConfig['methods'][strtolower($request->getMethod())];
        }

        // 2. Override if resource is set
        if (isset($request->attributes->get('_route_params')['_resource'])) {
            $resource = $request->attributes->get('_route_params')['_resource'];
            if (isset($allowConfig['resources'][$resource])) {
                $allowed = $allowConfig['resources'][$resource];
            }
        }

        // 3. Override by route
        $route = $request->attributes->get('_route');
        if (isset($allowConfig['routes'][$route])) {
            $allowed = $allowConfig['routes'][$route];
        }

        if (!$allowed) {
            throw new AccessDeniedHttpException("Access denied");
        }
    }
}
