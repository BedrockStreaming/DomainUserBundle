<?php

namespace M6Web\Bundle\DomainUserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class FirewallListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class UserAccessListener
{
    protected $tokenStorage;
    protected $allowDebugRoute;

    /**
     * Constructor
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage, $allowDebugRoute)
    {
        $this->tokenStorage = $tokenStorage;
        $this->allowDebugRoute = $allowDebugRoute;
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
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return;
        }
        $user        = $token->getUser();
        $request     = $event->getRequest();
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

        // 4. check if debug routes are allowed
        if (
            preg_match('/^_(wdt|profiler)/', $route) &&
            ($this->allowDebugRoute || $allowConfig['allow_debug_route'])
        ) {
            $allowed = true;
        }

        if (!$allowed) {
            throw new AccessDeniedHttpException("Access denied");
        }
    }
}
