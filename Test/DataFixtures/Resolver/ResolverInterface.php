<?php

namespace Oro\Bundle\PhpUnitBundle\Test\DataFixtures\Resolver;

/**
 * Interface for the value resolvers.
 */
interface ResolverInterface
{
    /**
     * Returns the resolved value.
     *
     * @param mixed $value
     * @return mixed
     */
    public function resolve($value);
}
