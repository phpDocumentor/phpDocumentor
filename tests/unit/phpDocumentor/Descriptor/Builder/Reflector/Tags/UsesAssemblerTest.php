<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use phpDocumentor\Reflection\Fqsen;

/**
 * Test class for phpDocumentor\Descriptor\Builder\Reflector\Tags\UsesAssembler
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\Tags\UsesAssembler
 * @covers ::<private>
 */
class UsesAssemblerTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var UsesAssembler $fixture */
    protected $fixture;

    /** @var ProjectDescriptorBuilder|m\MockInterface */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp()
    {
        $this->builderMock = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $this->fixture = new UsesAssembler();
        $this->fixture->setBuilder($this->builderMock);
    }

    /**
     * @covers ::create
     */
    public function testCreateUsesDescriptorFromUsesTagWhenReferenceIsRelativeClassnameNotInNamespaceAliasses()
    {
        // Arrange
        $name = 'uses';
        $description = 'a uses tag';
        $reference = '\ReferenceClass';
        $usesTagMock = $this->givenAUsesTag($description, $reference);

        // Act
        $descriptor = $this->fixture->create($usesTagMock);

        // Assert
        $this->assertSame($name, $descriptor->getName());
        $this->assertSame($description, (string) $descriptor->getDescription());
        $this->assertSame($reference, (string) $descriptor->getReference());
        $this->assertSame([], $descriptor->getErrors()->getAll());
    }

    protected function givenAUsesTag($description, $reference)
    {
        return new Uses(
            new Fqsen($reference),
            new DocBlock\Description($description)
        );
    }
}
