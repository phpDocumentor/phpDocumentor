<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser;

use Flyfinder\Specification\SpecificationInterface;
use League\Flysystem\FilesystemInterface;
use Mockery as m;
use phpDocumentor\DomainModel\Parser\Documentation\Api\Definition;
use phpDocumentor\DomainModel\Parser\ApiParsingCompleted;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\DocumentGroupFormat;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\ApiParsingCompleted
 * @covers ::<private>
 */
final class ApiParsingCompletedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::definition
     */
    public function testApiDocumentGroupDefinitionIsPassedToEvent()
    {
        $definition = new Definition(
            new DocumentGroupFormat('php'),
            m::mock(FilesystemInterface::class),
            m::mock(SpecificationInterface::class)
        );

        $event = new ApiParsingCompleted($definition);

        $this->assertSame($definition, $event->definition());
    }
}
