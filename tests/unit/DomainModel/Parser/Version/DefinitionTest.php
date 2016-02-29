<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser\Version;

use phpDocumentor\DomainModel\Parser\Documentation\DummyDocumentGroupDefinition;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\Version\Definition
 * @covers ::__construct
 */
class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::getVersionNumber
     * @covers ::getDocumentGroupDefinitions
     */
    public function itShouldReceiveAVersionNumberAndListOfDocumentGroupDefinitions()
    {
        $documentGroupDefinitions = [new DummyDocumentGroupDefinition()];
        $definition = new Definition(new Number('1.0.1'), $documentGroupDefinitions);

        $this->assertEquals(new Number('1.0.1'), $definition->getVersionNumber());
        $this->assertSame($documentGroupDefinitions, $definition->getDocumentGroupDefinitions());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function itShouldErrorIfTheDocumentGroupDefinitionsArrayContainsSomethingOtherThanADefinition()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Definition(new Number('1.0.1'), ['abc']);
    }
}
