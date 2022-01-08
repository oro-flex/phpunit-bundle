<?php

namespace Oro\Bundle\PhpUnitBundle\Test\DataFixtures;

/**
 * If a data fixture implements this interface the fixtures executor
 * will not clear the entity manager after this fixture.
 * This might be helpful if you need to load existing data to reference in nelmio/alice file.
 * @see \Oro\Bundle\PhpUnitBundle\Test\DataFixtures\DataFixturesExecutor
 */
interface InitialFixtureInterface
{
}
