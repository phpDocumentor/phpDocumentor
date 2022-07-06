<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Faker\Faker;

use function str_replace;

use const PHP_EOL;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\ApiAnalyzer
 */
final class ApiAnalyzerTest extends MockeryTestCase
{
    use Faker;

    /** @var ApiAnalyzer */
    private $fixture;

    protected function setUp(): void
    {
        $this->fixture = new ApiAnalyzer();
    }

    /**
     * @covers ::__toString
     */
    public function testIfStringOutputContainsAllCounters(): void
    {
        // Arrange
        $classDescriptor1 = $this->faker()->classDescriptor();
        $classDescriptor1->setParent($this->faker()->classDescriptor());

        $classDescriptor2 = $this->faker()->classDescriptor();
        $classDescriptor2->setParent('unResolved');

        $interface = $this->faker()->interfaceDescriptor();
        $interface->setParent(new Collection(['123']));

        $projectDescriptor = $this->faker()->apiSetDescriptorWithFiles(4);
        $projectDescriptor->getIndexes()->fetch('elements', new Collection())->add($classDescriptor1);
        $projectDescriptor->getIndexes()->fetch('elements', new Collection())->add($classDescriptor2);
        $projectDescriptor->getIndexes()->fetch('elements', new Collection())->add($interface);
        $projectDescriptor->getNamespace()->setChildren(new Collection([
            $this->faker()->namespaceDescriptor(),
            $this->faker()->namespaceDescriptor(),
            $this->faker()->namespaceDescriptor(),
        ]));

        $this->fixture->analyze($projectDescriptor);

        $expected = <<<TEXT
In the ProjectDescriptor are:
         4 files
         3 top-level namespaces
         1 unresolvable parent classes
         2 phpDocumentor\Descriptor\ClassDescriptor elements
         1 phpDocumentor\Descriptor\InterfaceDescriptor elements

TEXT;
        $expected = str_replace("\n", PHP_EOL, $expected);

        // Act
        $result = (string) $this->fixture;

        // Assert
        $this->assertSame($expected, $result);
    }
}
