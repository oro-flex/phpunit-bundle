<?php

namespace Oro\Bundle\PhpUnitBundle\Tests\Unit\DependencyInjection\Compiler;

use Oro\Bundle\PhpUnitBundle\DependencyInjection\Compiler\ClientCompilerPass;
use Oro\Bundle\PhpUnitBundle\Test\Client;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ClientCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    /** @var ClientCompilerPass */
    private $compiler;

    protected function setUp(): void
    {
        $this->compiler = new ClientCompilerPass();
    }

    public function testProcessNoProviderDefinition()
    {
        $container = new ContainerBuilder();

        $this->compiler->process($container);
    }

    public function testProcess()
    {
        $container = new ContainerBuilder();
        $clientDef = $container->register('test.client');

        $this->compiler->process($container);

        $this->assertEquals(Client::class, $clientDef->getClass());
    }
}
