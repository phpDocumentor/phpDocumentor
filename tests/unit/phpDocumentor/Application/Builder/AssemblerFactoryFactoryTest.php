<?php

declare(strict_types=1);

namespace phpDocumentor\Application\Builder;

use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;

final class AssemblerFactoryFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function test_can_create_a_default_assembler_factory()
    {
        $factory = AssemblerFactoryFactory::create(new ExampleFinder());

        $this->assertInstanceOf(AssemblerFactory::class, $factory);
    }

    /**
     * @dataProvider provideAssemblerPerCriteria
     */
    public function test_created_assembler_factory_will_create_the_expected_assembler_based_on_match(
        $criteria,
        ?string $expectedTypeOfAssembler
    ) {
        $this->markTestIncomplete('Populate provider with all options, including an invalid one (hence the null)');
    }

    public function provideAssemblerPerCriteria()
    {
        return [
            ['a', 'a']
        ];
    }
}
