<?php

namespace M6Web\Bundle\DomainUserBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class M6WebDomainUserExtension
 *
 * @author Adrien Samson <asamson.externe@m6.fr>
 */
class M6WebDomainUserExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $processedConfig = $this->processConfiguration(new Configuration(), $config);
        $container->setParameter('m6_web_domain_user.default_user', $processedConfig['default_user']);
        $container->setParameter('m6_web_domain_user.router_parameter', $processedConfig['router_parameter']);
        $container->setParameter('m6_web_domain_user.default_cache', $processedConfig['default_cache']);
        $container->setParameter('m6_web_domain_user.users_dir', $processedConfig['users_dir']);
    }
}
