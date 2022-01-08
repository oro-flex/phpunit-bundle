<?php

namespace Oro\Bundle\PhpUnitBundle\Test\DataFixtures\Resolver;

use Oro\Bundle\PhpUnitBundle\Test\DataFixtures\Collection;

/**
 * Interface for the value resolvers which aware about objet references.
 */
interface ReferencesAwareInterface
{
    /**
     * Sets the object collection to handle referential calls.
     */
    public function setReferences(Collection $references);
}
