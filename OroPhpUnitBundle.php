<?php

namespace Oro\Bundle\PhpUnitBundle;

use Oro\Bundle\PhpUnitBundle\DependencyInjection\Compiler\CheckReferenceCompilerPass;
use Oro\Bundle\PhpUnitBundle\DependencyInjection\Compiler\ClientCompilerPass;
use Oro\Bundle\PhpUnitBundle\DependencyInjection\Compiler\TagsInformationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The PhpUnitBundle bundle class.
 */
class OroPhpUnitBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TagsInformationPass());
        $container->addCompilerPass(new CheckReferenceCompilerPass());
        $container->addCompilerPass(new ClientCompilerPass());
    }
}
