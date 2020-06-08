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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;

final class ResolveInlineMarkersTest extends MockeryTestCase
{
    public function testExecuteSetsMarkers() : void
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

        $projectDescriptor = new ProjectDescriptor('test project');
        $projectDescriptor->getSettings()->setMarkers(['TODO']);
        $projectDescriptor->setFiles(new Collection([$fileDescriptor]));

        $fixture->execute($projectDescriptor);

        self::assertCount(1, $fileDescriptor->getMarkers());
    }
}
