<?php
/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;

final class ResolveInlineMarkersTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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
