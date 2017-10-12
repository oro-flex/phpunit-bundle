<?php

namespace Oro\Bundle\TestFrameworkBundle\BehatStatisticExtension\ServiceContainer;

use Behat\Testwork\Output\ServiceContainer\OutputExtension;
use Behat\Testwork\ServiceContainer\Extension as TestworkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\TestFrameworkBundle\BehatStatisticExtension\Model\FeatureStatistic;
use Oro\Bundle\TestFrameworkBundle\BehatStatisticExtension\ServiceContainer\Formatter\StatisticFormatterFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BehatStatisticExtension implements TestworkExtension
{
    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        /** @var OutputExtension $outputExtension */
        $outputExtension = $extensionManager->getExtension('formatters');
        $outputExtension->registerFormatterFactory(new StatisticFormatterFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->variableNode('connection')->info('Doctrine Dbal Connection for a DB')->end()
                ->scalarNode('branch_name_env')->defaultNull()->end()
                ->scalarNode('target_branch_env')->defaultNull()->end()
                ->scalarNode('build_id_env')->defaultNull()->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $container->setParameter('statistic.branch_name', getenv($config['branch_name_env']) ?: null);
        $container->setParameter('statistic.target_branch', getenv($config['target_branch_env']) ?: null);
        $container->setParameter('statistic.build_id', getenv($config['build_id_env']) ?: null);
        $this->createDatabaseConnection($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function createDatabaseConnection(ContainerBuilder $container, array $config)
    {
        $connection = DriverManager::getConnection($config['connection'], new Configuration());
        $container->set('behat_statistic.database.connection', $connection);
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->upgradeSchema($container);
    }

    /**
     * @param ContainerBuilder $container
     * @throws \Doctrine\DBAL\DBALException In case if db does not exists
     */
    private function upgradeSchema(ContainerBuilder $container)
    {
        /** @var Connection $connection */
        $connection = $container->get('behat_statistic.database.connection');
        try {
            $connection->ping();
        } catch (ConnectionException $e) {
            throw new DBALException('Exception while connect to db', 0, $e);
        }

        $schema = new Schema();
        FeatureStatistic::declareSchema($schema);

        $currentSchema = $connection->getSchemaManager()->createSchema();

        $comparator = new Comparator();
        $schemaDiff = $comparator->compare($currentSchema, $schema);

        $queries = $schemaDiff->toSql($connection->getDatabasePlatform());

        foreach ($queries as $query) {
            $connection->query($query);
        }

        $connection->close();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'behat_statistic';
    }
}
