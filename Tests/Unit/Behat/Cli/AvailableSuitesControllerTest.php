<?php

namespace Oro\Bundle\TestFrameworkBundle\Tests\Unit\Behat\Cli;

use Behat\Testwork\Specification\SpecificationFinder;
use Behat\Testwork\Suite\Generator\GenericSuiteGenerator;
use Behat\Testwork\Suite\Suite;
use Behat\Testwork\Suite\SuiteRegistry;
use Oro\Bundle\TestFrameworkBundle\Behat\Cli\AvailableSuitesController;
use Oro\Bundle\TestFrameworkBundle\Tests\Unit\Behat\Cli\Stub\SpecificationLocatorStub;
use Oro\Component\Testing\Unit\Command\Stub\InputStub;
use Oro\Component\Testing\Unit\Command\Stub\OutputStub;

class AvailableSuitesControllerTest extends \PHPUnit_Framework_TestCase
{
    private $suites = [
        'One',
        'Two',
        'Three',
        'Four',
        'Five',
        'Six',
    ];

    public function testSkipExecutionWithoutOptions()
    {
        $suiteRegistry = new SuiteRegistry();
        $specificationFinder = new SpecificationFinder();
        $controller = new AvailableSuitesController($suiteRegistry, $specificationFinder, []);
        $returnCode = $controller->execute(new InputStub(), new OutputStub());

        self::assertNull($returnCode);
    }

    /**
     * @dataProvider getSuitesWithSpecifications
     */
    public function testAvailableSuites(array $suitesWithSpecifications)
    {
        $suiteRegistry = new SuiteRegistry();
        $suiteRegistry->registerSuiteGenerator(new GenericSuiteGenerator());
        $suiteConfigurations = $this->createFakeConfigurations($this->suites);
        array_walk($suiteConfigurations, function (array $suiteConfig, $suiteName) use ($suiteRegistry) {
            $suiteRegistry->registerSuiteConfiguration($suiteName, $suiteConfig['type'], $suiteConfig['settings']);
        });

        $specificationFinder = new SpecificationFinder();
        $specificationLocator = new SpecificationLocatorStub($suitesWithSpecifications);
        $specificationFinder->registerSpecificationLocator($specificationLocator);

        $controller = new AvailableSuitesController($suiteRegistry, $specificationFinder);

        $input = new InputStub('', [], ['available-suites' => true]);
        $output = new OutputStub();

        $returnCode = $controller->execute($input, $output);
        self::assertSame($suitesWithSpecifications, $output->messages);
        self::assertSame(0, $returnCode);
    }

    public function getSuitesWithSpecifications()
    {
        return [
            [['Two', 'Six']],
            [['Three', 'Four', 'Five']],
            [['One', 'Three', 'Five']],
        ];
    }

    /**
     * @param array $availableSuites in format ['SuiteNameOne', 'SuiteNameTwo']
     * @return array in format ['SuiteNameOne' => ['type' => null, 'settings' => []]]
     */
    private function createFakeConfigurations(array $availableSuites)
    {
        return array_map(function () {
            return [
                'type' => null,
                'settings' => [],
            ];
        }, array_flip($availableSuites));
    }
}
