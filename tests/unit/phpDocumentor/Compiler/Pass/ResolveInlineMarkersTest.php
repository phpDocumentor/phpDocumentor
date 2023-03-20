<?php

declare(strict_types=1);

/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      https://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

final class ResolveInlineMarkersTest extends TestCase
{
    use Faker;

    public function testExecuteSetsMarkers(): void
    {
        $fixture = new ResolveInlineMarkers();

        $fileDescriptor = new FileDescriptor('abc');
        $fileDescriptor->setSource(
            <<<SOURCE
                <?php
                
                class Marker
                {
                   //TODO: implement this
                }      
SOURCE
        );

        $apiDescriptor = $this->faker()->apiSetDescriptor();
        $documentationsSets = Collection::fromClassString(DocumentationSetDescriptor::class);
        $documentationsSets->add($apiDescriptor);

        $projectDescriptor = new ProjectDescriptor('test project');
        $projectDescriptor->getVersions()->add(new VersionDescriptor('latest', $documentationsSets));
        $apiDescriptor->setFiles(new Collection([$fileDescriptor]));

        $fixture->execute($projectDescriptor);

        self::assertCount(1, $fileDescriptor->getMarkers());
    }
}
