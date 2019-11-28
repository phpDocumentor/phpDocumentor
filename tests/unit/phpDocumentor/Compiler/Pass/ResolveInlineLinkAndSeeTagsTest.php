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

namespace phpDocumentor\Compiler\Pass;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Router\Rule;
use phpDocumentor\Transformer\Router\StandardRouter;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Pass\ResolveInlineLinkAndSeeTags
 * @covers ::__construct
 * @covers ::<private>
 */
class ResolveInlineLinkAndSeeTagsTest extends MockeryTestCase
{
    /** @var StandardRouter|MockInterface */
    private $router;

    /** @var ResolveInlineLinkAndSeeTags */
    private $fixture;

    /**
     * Initializes the fixture and its dependencies.
     */
    protected function setUp(): void
    {
        $this->router = m::mock(StandardRouter::class);
        $this->fixture = new ResolveInlineLinkAndSeeTags($this->router);
    }

    /**
     * @covers ::getDescription
     */
    public function testDescriptionName() : void
    {
        $this->assertSame('Resolve @link and @see tags in descriptions', $this->fixture->getDescription());
    }

    /**
     * @covers ::execute
     */
    public function testReplaceDescriptionIfItContainsNoSeeOrLink() : void
    {
        $description = 'This is a description';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $description);

        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project);
    }

    /**
     * @covers ::execute
     */
    public function testReplaceDescriptionIfItContainsASeeButFileIsNotAvailable() : void
    {
        $description = 'Description with {@see ARandomDescriptor}';
        $expected = 'Description with \ARandomDescriptor';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);
        $elementToLinkTo = $this->givenAnElementToLinkTo();

        $this->whenDescriptionContainsSeeOrLinkWithElement($descriptor, $elementToLinkTo);

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project);
    }

    /**
     * @covers ::execute
     */
    public function testReplaceDescriptionIfItContainsASeeAndFileIsPresent() : void
    {
        $description = 'Description with {@see LinkDescriptor}';
        $expected = 'Description with [\phpDocumentor\LinkDescriptor](../classes/phpDocumentor.LinkDescriptor.html)';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);
        $elementToLinkTo = $this->givenAnElementToLinkTo();

        $this->whenDescriptionContainsSeeOrLinkWithElement($descriptor, $elementToLinkTo);

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project);
    }

    /**
     * @covers ::execute
     */
    public function testReplaceDescriptionIfItContainsAnotherTag() : void
    {
        $description = 'Description with {@author John Doe}';
        $expected = 'Description with {@author John Doe}';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project);
    }

    /**
     * Returns a mocked Descriptor with its description set to the given value.
     *
     * @param string $description
     *
     * @return MockInterface
     */
    private function givenAChildDescriptorWithDescription($description) : MockInterface
    {
        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getDescription')->andReturn($description);

        return $descriptor;
    }

    /**
     * Returns a mocked Project Descriptor.
     *
     * @param Collection|MockInterface $descriptors
     *
     * @return MockInterface
     */
    private function givenAProjectDescriptorWithChildDescriptors($descriptors) : MockInterface
    {
        $projectDescriptor = m::mock(ProjectDescriptor::class);
        $projectDescriptor->shouldReceive('getIndexes')->andReturn($descriptors);

        return $projectDescriptor;
    }

    /**
     * Returns the descriptor of the element that the link points to
     *
     * @return DescriptorAbstract|MockInterface
     */
    private function givenAnElementToLinkTo()
    {
        $namespaceAliases = ['LinkDescriptor' => '\phpDocumentor\LinkDescriptor'];
        $namespaceCollection = m::mock(Collection::class);
        $namespaceCollection->shouldReceive('getAll')->once()->andReturn($namespaceAliases);

        $elementToLinkTo = m::mock(DescriptorAbstract::class);
        $elementToLinkTo->shouldReceive('getNamespaceAliases')->once()->andReturn($namespaceCollection);

        return $elementToLinkTo;
    }

    /**
     * Returns a collection with descriptor. This collection will be scanned to see if a link can be made to a file.
     *
     * @param DescriptorAbstract|MockInterface $descriptor
     *
     * @return Collection|MockInterface
     */
    private function givenACollection($descriptor)
    {
        $collection = m::mock(Collection::class);

        $items = ['\phpDocumentor\LinkDescriptor' => $descriptor];

        $collection->shouldReceive('get')->once()->andReturn($items);

        return $collection;
    }

    /**
     * Verifies if the given descriptor's setDescription method is called with the given value.
     *
     * @param MockInterface $descriptor
     * @param string          $expected
     */
    public function thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected) : void
    {
        $descriptor->shouldReceive('setDescription')->with($expected);
    }

    /**
     * It resolves the element that is linked to
     *
     * @param MockInterface $descriptor
     * @param DescriptorAbstract $elementToLinkTo
     *
     * @return DescriptorAbstract
     */
    private function whenDescriptionContainsSeeOrLinkWithElement($descriptor, $elementToLinkTo) : DescriptorAbstract
    {
        $rule = m::mock(Rule::class);
        $rule->shouldReceive('generate')->andReturn('/classes/phpDocumentor.LinkDescriptor.html');
        $this->router->shouldReceive('match')->andReturn($rule);
        $descriptor->shouldReceive('getFile')->andReturn($elementToLinkTo);
        $descriptor->shouldReceive('getNamespace');

        return $descriptor;
    }
}
