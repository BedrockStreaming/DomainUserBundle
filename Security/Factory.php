<?php

namespace M6Web\Bundle\DomainUserBundle\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;

/**
 * Class Factory
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class Factory implements SecurityFactoryInterface
{
    /**
     * Create
     *
     * @param ContainerBuilder $container
     * @param string           $id
     * @param mixed            $config
     * @param mixed            $userProvider
     * @param mixed            $defaultEntryPoint
     *
     * @return array
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.m6_web_domain_user.'.$id;
        $container
            ->setDefinition($providerId, new ChildDefinition('m6_web_domain_user.security.authentication_provider'))
            ->replaceArgument(0, new Reference($userProvider));

        $listenerId = 'security.authentication.listener.m6_web_domain_user.'.$id;
        $container->setDefinition($listenerId, new ChildDefinition('m6_web_domain_user.security.authentication_listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * Get Position
     *
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return 'm6_web_domain_user';
    }

    /**
     * Add Config
     *
     * @param NodeDefinition $node
     *
     * @return void
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
