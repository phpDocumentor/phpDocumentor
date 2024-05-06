<?php

declare(strict_types=1);

namespace functional\phpDocumentor\core;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\FunctionalTestCase;

final class VisibilityFilterFunctionalTest extends FunctionalTestCase
{
    public function testDefaultVisibilityFilter() : void
    {
        $this->runPHPDocWithFile(__DIR__ .'/../../assets/core/visibility.php');
        $project = $this->loadAst();

        $versions = $project->getVersions();
        $this->assertCount(1, $versions);

        $apiSets = $versions->first()->getDocumentationSets()->filter(ApiSetDescriptor::class);
        $this->assertCount(1, $apiSets);

        /** @var ApiSetDescriptor $apiSet */
        $apiSet = $apiSets->first();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $apiSet->getIndexes()->get('classes')->get('\Visibility');

        $this->assertCount(3, $classDescriptor->getMethods());
        $this->assertCount(3, $classDescriptor->getProperties());
        $this->assertCount(3, $classDescriptor->getConstants());
    }

    /**
     * @dataProvider visibilityProvider
     */
    public function testVisibilityFilterByCli(string $visability, int $expectedCount) : void
    {
        $this->runPHPDocWithFile(__DIR__ .'/../../assets/core/visibility.php', ['--visibility='.$visability]);
        $project = $this->loadAst();

        $versions = $project->getVersions();
        $this->assertCount(1, $versions);

        $apiSets = $versions->first()->getDocumentationSets()->filter(ApiSetDescriptor::class);
        $this->assertCount(1, $apiSets);

        /** @var ApiSetDescriptor $apiSet */
        $apiSet = $apiSets->first();

        /** @var ClassDescriptor $classDescriptor */
        $classDescriptor = $apiSet->getIndexes()->get('classes')->get('\Visibility');

        $this->assertCount($expectedCount, $classDescriptor->getMethods());
        $this->assertCount($expectedCount, $classDescriptor->getProperties());
        $this->assertCount($expectedCount, $classDescriptor->getConstants());
    }

    public static function visibilityProvider() : array
    {
        return [
            [
                'public',
                1
            ],
            [
                'public,protected',
                2
            ],
            [
                'protected',
                1
            ],
            [
                'private',
                1
            ]
        ];
    }
}
