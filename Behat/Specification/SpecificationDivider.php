<?php

namespace Oro\Bundle\TestFrameworkBundle\Behat\Specification;

use Behat\Gherkin\Node\FeatureNode;
use Behat\Testwork\Specification\SpecificationFinder;
use Behat\Testwork\Suite\Exception\SuiteConfigurationException;
use Behat\Testwork\Suite\Generator\GenericSuiteGenerator;
use Behat\Testwork\Suite\GenericSuite;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Suite\SuiteRegistry;
use Guzzle\Iterator\ChunkedIterator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SpecificationDivider
{
    /**
     * Divide suite by features count
     * Divide suites by sets
     *
     * Each generated chunk will has number of elements provided by divider
     * E.g. if 'AcmeSuite' suite has 14 features and divider is 5,
     *      3 suites will be generated
     *      'AcmeSuite#0' and 'AcmeSuite#1' with 5 features each
     *      and 'AcmeSuite#2' with 4 features
     *
     * @param string $suiteName e.g. AcmeSuite
     * @param array $paths Paths to feature files or directories with feature files
     * @param int $divider
     * @return array [
     *                 'AcmeSuite#0' => ['/path/to/first.feature', '/path/to/second.feature],
     *                 'AcmeSuite#1' => ['/path/to/third.feature'],
     *               ]
     */
    public function divide($suiteName, array $paths, $divider)
    {
        $generatedSuites = [];

        $chunks = $this->getChunks($paths, $divider);
        foreach ($chunks as $index => $chunk) {
            $generatedSuiteName = $suiteName.'#'.$index;
            $generatedSuites[$generatedSuiteName] = $chunk;
        }

        return $generatedSuites;
    }

    private function getChunks(array $paths, $divider)
    {
        $count = count($paths);
        $chunks = array_chunk($paths, $divider);

        if (0 === $count%$divider) {
            return $chunks;
        }

        if (2 > count($chunks)) {
            return $chunks;
        }

        $tail = array_merge(array_pop($chunks), array_pop($chunks));
        $tailChunks = array_chunk($tail, round(count($tail)/2));

        array_push($chunks, $tailChunks[0], $tailChunks[1]);

        return $chunks;
    }
}
