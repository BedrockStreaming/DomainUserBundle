<?php

namespace M6Web\Bundle\DomainUserBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


/**
 * Class Configuration
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('m6_web_domain_user');

        $rootNode->children()
            ->scalarNode('default_user')->isRequired()->end()
            ->scalarNode('default_cache')->isRequired()->end()
            ->scalarNode('router_parameter')->isRequired()->end()
            ->scalarNode('users_dir')->isRequired()->end()
            ->arrayNode('firewall')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('allow_debug_route')->defaultFalse()->end();

        return $treeBuilder;
    }

}
