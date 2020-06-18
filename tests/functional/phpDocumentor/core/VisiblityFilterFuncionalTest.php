<?php

declare(strict_types=1);

namespace functional\phpDocumentor\core;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\FunctionalTestCase;

final class VisiblityFilterFuncionalTest extends FunctionalTestCase
{
    public function testDefaultVisiblityFilter() : void
    {
        $this->runPHPDocWithFile(__DIR__ .'/../../assets/core/visibility.php');
        $project = $this->loadAst();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $project->getIndexes()->get('classes')->get('\Visibility');

        $this->assertCount(3, $classDescriptor->getMethods());
        $this->assertCount(3, $classDescriptor->getProperties());
        $this->assertCount(3, $classDescriptor->getConstants());
    }

    /**
     * @dataProvider visibilityProvider
     */
    public function testVisiblityFilterByCli(string $visability, int $expectedCount) : void
    {
        $this->runPHPDocWithFile(__DIR__ .'/../../assets/core/visibility.php', ['--visibility='.$visability]);
        $project = $this->loadAst();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $project->getIndexes()->get('classes')->get('\Visibility');

        $this->assertCount($expectedCount, $classDescriptor->getMethods());
        $this->assertCount($expectedCount, $classDescriptor->getProperties());
        $this->assertCount($expectedCount, $classDescriptor->getConstants());
    }

    public function visibilityProvider() : array
    {
        return [
            [
                'public',
                1
            ]
        ];
    }
}
