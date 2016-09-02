<?php

namespace M6Web\Bundle\DomainUserBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class CacheListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class CacheListener
{
    protected $defaultCache;
    protected $tokenStorage;

    /**
     * Constructor
     *
     * @param TokenStorageInterface $tokenStorage
     * @param integer               $defaultCache
     */
    public function __construct(TokenStorageInterface $tokenStorage, $defaultCache)
    {
        $this->tokenStorage = $tokenStorage;
        $this->defaultCache    = $defaultCache;
    }

    /**
     * Add Cache on response
     *
     * @param FilterResponseEvent $event
     *
     * @return void
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $response = $event->getResponse();
        $request  = $event->getRequest();
        if ($request->getMethod() !== 'GET' || !$response->isSuccessful() || $response->headers->hasCacheControlDirective('max-age')) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return;
        }
        $user        = $token->getUser();
        $cacheConfig = $user->getConfigCache();

        $cache = $this->defaultCache;
        if (isset($cacheConfig['default'])) {
            $cache = $cacheConfig['default'];
        }
        $route = $event->getRequest()->attributes->get('_route');
        if (isset($cacheConfig['routes'][$route])) {
            $cache = $cacheConfig['routes'][$route];
        }

        if ($cache > 0) {
            $response->headers->set('Cache-Control', sprintf('max-age=%d, public', $cache));
        }
    }
}
