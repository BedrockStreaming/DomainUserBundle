<?php

namespace M6Web\Bundle\DomainUserBundle\User;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class UserConfiguration
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class UserConfiguration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('')
            ->children()
                ->arrayNode('cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')->defaultValue(300)->end()
                        ->arrayNode('routes')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('entities')
                    ->prototype('array')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()

                ->arrayNode('parameters')
                    ->prototype('variable')->end()
                ->end()

                ->arrayNode('firewall')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('user_access')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('default_state')->defaultTrue()->end()
                                ->arrayNode('lists')
                                    ->useAttributeAsKey('name')
                                    ->prototype('boolean')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('allow')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('allow_debug_route')->defaultFalse()->end()
                                ->booleanNode('default')->defaultFalse()->end()
                                ->arrayNode('methods')
                                    ->prototype('boolean')->end()
                                ->end()
                                ->arrayNode('resources')
                                    ->useAttributeAsKey('name')
                                    ->prototype('boolean')->end()
                                ->end()
                                ->arrayNode('routes')
                                    ->useAttributeAsKey('name')
                                    ->prototype('boolean')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();

        return $treeBuilder;
    }
}
