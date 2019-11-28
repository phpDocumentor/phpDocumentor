<?php declare(strict_types=1);

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

namespace phpDocumentor\Transformer\Router;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Type\CollectionDescriptor;

/**
 * Test class for phpDocumentor\Transformer\Router\Renderer
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Router\Renderer
 * @covers ::<private>
 */
final class RendererTest extends MockeryTestCase
{
    /** @var Router */
    private $router;

    /** @var Renderer */
    private $renderer;

    protected function setUp(): void
    {
        $this->router = m::mock(Router::class);

        $this->renderer = new Renderer($this->router);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Renderer::__construct
     * @covers \phpDocumentor\Transformer\Router\Renderer::getDestination
     * @covers \phpDocumentor\Transformer\Router\Renderer::setDestination
     */
    public function testGetAndSetDestination(): void
    {
        $this->renderer->setDestination('destination');

        $this->assertSame('destination', $this->renderer->getDestination());
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithFqsenAndRepresentationUrl(): void
    {
        $rule = $this->givenARule('/classes/My.Namespace.Class.html');
        $this->router->shouldReceive('match')->andReturn($rule);

        $result = $this->renderer->render('\My\Namespace\Class', 'url');

        $this->assertSame('classes/My.Namespace.Class.html', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionOfFqsensAndRepresentationUrl(): void
    {
        $rule = $this->givenARule('/classes/My.Namespace.Class.html');
        $this->router->shouldReceive('match')->andReturn($rule);

        $this->renderer->setDestination(str_replace('/', DIRECTORY_SEPARATOR, '/root/of/project'));
        $collection = new Collection(['\My\Namespace\Class']);
        $result = $this->renderer->render($collection, 'url');

        $this->assertSame(['../../../classes/My.Namespace.Class.html'], $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithUrlAndNoRuleMatch(): void
    {
        $rule = $this->givenARule('@');
        $this->router->shouldReceive('match')->with('file://phpdoc')->andReturn($rule);
        $this->router->shouldReceive('match')->with('@')->andReturn(null);

        $result = $this->renderer->render('file://phpdoc', 'url');

        $this->assertNull($result);
    }

    /**
     * @covers ::convertToRootPath
     */
    public function testConvertToRootPathWithUrlAndAtSignInRelativePath(): void
    {
        $rule = $this->givenARule('@Class::$property');
        $this->router->shouldReceive('match')->with('@Class::$property')->andReturn($rule);

        $result = $this->renderer->convertToRootPath('@Class::$property');

        $this->assertSame('@Class::$property', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionDescriptorWithNameIsNotArrayAndRepresentationUrl(): void
    {
        $rule = $this->givenARule('ClassDescriptor');
        $this->router->shouldReceive('match')->andReturn($rule);

        $collectionDescriptor = $this->givenACollectionDescriptor('class');
        $collectionDescriptor->setKeyTypes(['ClassDescriptor']);
        $result = $this->renderer->render($collectionDescriptor, 'url');

        $this->assertSame('ClassDescriptor&lt;ClassDescriptor,ClassDescriptor&gt;', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionDescriptorWithNameIsArrayAndRepresentationUrl(): void
    {
        $rule = $this->givenARule('ClassDescriptor');
        $this->router->shouldReceive('match')->andReturn($rule);

        $collectionDescriptor = $this->givenACollectionDescriptor('array');
        $result = $this->renderer->render($collectionDescriptor, 'url');

        $this->assertSame('ClassDescriptor[]', $result);
    }

    /**
     * @covers ::render
     */
    public function testRenderWithFqsenAndRepresentationClassShort(): void
    {
        $rule = $this->givenARule('/classes/My.Namespace.Class.html');
        $this->router->shouldReceive('match')->andReturn($rule);

        $result = $this->renderer->render('\My\Namespace\Class', 'class:short');

        $this->assertSame('<a href="classes/My.Namespace.Class.html">Class</a>', $result);
    }

    /**
     * @covers ::render
     * @dataProvider provideUrls
     */
    public function testRenderWithUrl(string $url): void
    {
        $rule = $this->givenARule($url);
        $this->router->shouldReceive('match')->andReturn($rule);

        $result = $this->renderer->render($url, 'url');

        $this->assertSame($url, $result);
    }

    private function givenARule(string $returnValue): Rule
    {
        $rule = m::mock('phpDocumentor\Transformer\Router\Rule');
        $rule->shouldReceive('generate')->andReturn($returnValue);

        return $rule;
    }

    private function givenACollectionDescriptor(string $name): CollectionDescriptor
    {
        $classDescriptor = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $classDescriptor->shouldReceive('getName')->andReturn($name);
        $collectionDescriptor = new CollectionDescriptor($classDescriptor);
        $collectionDescriptor->setTypes(['ClassDescriptor']);
        return $collectionDescriptor;
    }

    public function provideUrls(): array
    {
        return [
            ['http://phpdoc.org'],
            ['https://phpdoc.org'],
            ['ftp://phpdoc.org'],
        ];
    }
}
