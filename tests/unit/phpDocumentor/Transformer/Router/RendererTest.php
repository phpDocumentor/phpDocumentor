<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Type\CollectionDescriptor;

/**
 * Test class for phpDocumentor\Transformer\Router\Renderer
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Router\Renderer
 * @covers ::<protected>
 */
class RendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Queue
     */
    private $originalQueue;

    /**
     * @var Renderer
     */
    private $renderer;

    public function setUp()
    {
        $this->originalQueue = m::mock('phpDocumentor\Transformer\Router\Queue');
        $this->renderer = new Renderer($this->originalQueue);
    }


    /**
     * @covers ::__construct
     * @covers ::getRouters
     */
    public function testConstructRenderer()
    {
        $result = $this->renderer->getRouters();

        $this->assertSame($this->originalQueue, $result);
    }

    /**
     * @covers ::__construct
     * @covers ::getRouters
     * @covers ::setRouters
     */
    public function testGetAndSetRouters()
    {
        $rule = $this->givenARule('http://phpdoc.org');
        $newQueue = [$this->givenAQueue($rule)];
        $this->renderer->setRouters($newQueue);

        $result = $this->renderer->getRouters();

        $this->assertNotSame($this->originalQueue, $result);
        $this->assertSame($newQueue, $result);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Renderer::__construct
     * @covers \phpDocumentor\Transformer\Router\Renderer::getDestination
     * @covers \phpDocumentor\Transformer\Router\Renderer::setDestination
     */
    public function testGetAndSetDestination()
    {
        $this->renderer->setDestination('destination');

        $this->assertSame('destination', $this->renderer->getDestination());
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithFqsenAndRepresentationUrl()
    {
        $rule = $this->givenARule('/classes/My.Namespace.Class.html');
        $queue = $this->givenAQueue($rule);
        $queue->shouldReceive('match')->andReturn($rule);
        $this->renderer->setRouters($queue);
        $result = $this->renderer->render('\My\Namespace\Class', 'url');

        $this->assertSame('classes/My.Namespace.Class.html', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionOfFqsensAndRepresentationUrl()
    {
        $rule = $this->givenARule('/classes/My.Namespace.Class.html');
        $queue = $this->givenAQueue($rule);
        $queue->shouldReceive('match')->andReturn($rule);
        $this->renderer->setRouters($queue);
        $this->renderer->setDestination(str_replace('/', DIRECTORY_SEPARATOR,'/root/of/project'));
        $collection = new Collection(['\My\Namespace\Class']);
        $result = $this->renderer->render($collection, 'url');

        $this->assertSame(['../../../classes/My.Namespace.Class.html'], $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithUrlAndNoRuleMatch()
    {
        $rule = $this->givenARule('@');
        $queue = $this->givenAQueue($rule);
        $queue->shouldReceive('match')->with('file://phpdoc')->andReturn($rule);
        $queue->shouldReceive('match')->with('@')->andReturn(null);
        $this->renderer->setRouters($queue);
        $result = $this->renderer->render('file://phpdoc', 'url');

        $this->assertSame(null, $result);
    }

    /**
     * @covers ::convertToRootPath
     */
    public function testConvertToRootPathWithUrlAndAtSignInRelativePath()
    {
        $rule = $this->givenARule('@Class::$property');
        $queue = $this->givenAQueue($rule);
        $queue->shouldReceive('match')->with('@Class::$property')->andReturn($rule);
        $this->renderer->setRouters($queue);
        $result = $this->renderer->convertToRootPath('@Class::$property');

        $this->assertSame('@Class::$property', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionDescriptorWithNameIsNotArrayAndRepresentationUrl()
    {
        $rule = $this->givenARule('ClassDescriptor');
        $queue = $this->givenAQueue($rule);
        $queue->shouldReceive('match')->andReturn($rule);
        $this->renderer->setRouters($queue);
        $collectionDescriptor = $this->givenACollectionDescriptor('class');
        $collectionDescriptor->setKeyTypes(['ClassDescriptor']);
        $result = $this->renderer->render($collectionDescriptor, 'url');

        $this->assertSame('ClassDescriptor&lt;ClassDescriptor,ClassDescriptor&gt;', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionDescriptorWithNameIsArrayAndRepresentationUrl()
    {
        $rule = $this->givenARule('ClassDescriptor');
        $queue = $this->givenAQueue($rule);
        $queue->shouldReceive('match')->andReturn($rule);
        $this->renderer->setRouters($queue);
        $collectionDescriptor = $this->givenACollectionDescriptor('array');
        $result = $this->renderer->render($collectionDescriptor, 'url');

        $this->assertSame('ClassDescriptor[]', $result);
    }

    /**
     * @covers ::render
     */
    public function testRenderWithFqsenAndRepresentationClassShort()
    {
        $rule = $this->givenARule('/classes/My.Namespace.Class.html');
        $queue = $this->givenAQueue($rule);
        $queue->shouldReceive('match')->andReturn($rule);
        $this->renderer->setRouters($queue);
        $result = $this->renderer->render('\My\Namespace\Class', 'class:short');

        $this->assertSame('<a href="classes/My.Namespace.Class.html">Class</a>', $result);
    }

    /**
     * @covers ::render
     * @dataProvider provideUrls
     */
    public function testRenderWithUrl($url)
    {
        $rule = $this->givenARule($url);
        $queue = $this->givenAQueue($rule);
        $queue->shouldReceive('match')->andReturn($rule);
        $this->renderer->setRouters($queue);
        $result = $this->renderer->render($url, 'url');

        $this->assertSame($url, $result);
    }

    /**
     * $param string $returnValue
     * @return m\MockInterface
     */
    protected function givenARule($returnValue)
    {
        $rule = m::mock('phpDocumentor\Transformer\Router\Rule');
        $rule->shouldReceive('generate')->andReturn($returnValue);
        return $rule;
    }

    /**
     * @param string $name
     * @return CollectionDescriptor
     */
    protected function givenACollectionDescriptor($name)
    {
        $classDescriptor = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $classDescriptor->shouldReceive('getName')->andReturn($name);
        $collectionDescriptor = new CollectionDescriptor($classDescriptor);
        $collectionDescriptor->setTypes(['ClassDescriptor']);
        return $collectionDescriptor;
    }

    /**
     * @param Rule $rule
     * @return m\MockInterface
     */
    private function givenAQueue($rule)
    {
        $queue = m::mock('phpDocumentor\Transformer\Router\Queue');
        $router = m::mock('phpDocumentor\Transformer\Router\StandardRouter');
        $queue->shouldReceive('insert');
        $router->shouldReceive('match')->andReturn($rule);
        return $queue;
    }

    /**
     * @return array
     */
    public function provideUrls()
    {
        return [
            ['http://phpdoc.org'],
            ['https://phpdoc.org'],
            ['ftp://phpdoc.org']
        ];
    }
}
