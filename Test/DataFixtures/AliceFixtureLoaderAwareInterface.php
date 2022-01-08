<?php

namespace Oro\Bundle\PhpUnitBundle\Test\DataFixtures;

interface AliceFixtureLoaderAwareInterface
{
    public function setLoader(AliceFixtureLoader $loader);
}
