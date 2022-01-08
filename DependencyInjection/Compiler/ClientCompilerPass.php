<?php

namespace Oro\Bundle\PhpUnitBundle\DependencyInjection\Compiler;

use Oro\Bundle\PhpUnitBundle\Test\Client;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ClientCompilerPass implements CompilerPassInterface
{
    const CLIENT_SERVICE = 'test.client';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(self::CLIENT_SERVICE)) {
            $definition = $container->getDefinition(self::CLIENT_SERVICE);
            $definition->setClass(Client::class);
        }
    }
}
