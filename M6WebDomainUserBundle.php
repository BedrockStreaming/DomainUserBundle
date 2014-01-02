<?php

namespace M6Web\Bundle\DomainUserBundle;

use M6Web\Bundle\DomainUserBundle\Security\Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class M6WebDomainUserBundle
 *
 * @package M6Web\Bundle\DomainUserBundle
 */
class M6WebDomainUserBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new Factory());
    }
}
