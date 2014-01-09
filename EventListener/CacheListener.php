<?php

namespace M6Web\Bundle\DomainUserBundle\EventListener;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Class CacheListener
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class CacheListener
{
    protected $defaultCache;
    protected $securityContext;

    /**
     * Constructor
     *
     * @param SecurityContextInterface $securityContext
     * @param integer                  $defaultCache
     */
    public function __construct(SecurityContextInterface $securityContext, $defaultCache)
    {
        $this->securityContext = $securityContext;
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
        $response = $event->getResponse();
        $request  = $event->getRequest();
        if ($request->getMethod() !== 'GET' || !$response->isSuccessful() || $response->headers->hasCacheControlDirective('max-age')) {
            return;
        }

        $token = $this->securityContext->getToken();
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
