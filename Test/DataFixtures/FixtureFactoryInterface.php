<?php

namespace Oro\Bundle\PhpUnitBundle\Test\DataFixtures;

interface FixtureFactoryInterface
{
    /**
     * Creates a fixture instance by its identifier.
     *
     * @param string $fixtureId
     *
     * @return object
     */
    public function createFixture($fixtureId);
}
