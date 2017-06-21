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

namespace phpDocumentor\Plugin\Core\Xslt;

use Mockery as m;

/**
 * Test class for \phpDocumentor\Plugin\Core\Xslt\Extension.
 *
 * @covers \phpDocumentor\Plugin\Core\Xslt\Extension
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \phpDocumentor\Plugin\Core\Xslt\Extension::markdown
     */
    public function testMarkdownWithString()
    {
        $text = '`this is markdown`';

        $result = Extension::markdown($text);

        $this->assertSame("<p><code>this is markdown</code></p>", $result);
    }

    /**
     * @covers \phpDocumentor\Plugin\Core\Xslt\Extension::markdown
     */
    public function testMarkdownReturnsInputUnchangedWhenInputIsNotString()
    {
        $text = [];

        $result = Extension::markdown($text);

        $this->assertSame([], $result);
    }

    /**
     * @covers \phpDocumentor\Plugin\Core\Xslt\Extension::path
     */
    public function testPathWithExternalLink()
    {
        $elementList = m::mock('phpDocumentor\Descriptor\Collection');
        $elementList->shouldReceive('get')->andReturn(null);

        $projectDescriptorBuilder = $this->givenAProjectDescriptorBuilder($elementList);

        $rule = m::mock('phpDocumentor\Transformer\Router');
        $rule->shouldReceive('generate')->andReturn('http://phpdoc.org');
        $router = $this->givenARouter($rule);

        Extension::$descriptorBuilder = $projectDescriptorBuilder;
        Extension::$routers = $router;
        $result = Extension::path('http://phpdoc.org');

        $this->assertSame('http://phpdoc.org', $result);
    }

    /**
     * @covers \phpDocumentor\Plugin\Core\Xslt\Extension::path
     */
    public function testPathWithUndocumentedElement()
    {
        $elementList = m::mock('phpDocumentor\Descriptor\Collection');
        $elementList->shouldReceive('get')->andReturn(null);

        $projectDescriptorBuilder = $this->givenAProjectDescriptorBuilder($elementList);

        $router = $this->givenARouter(null);

        Extension::$descriptorBuilder = $projectDescriptorBuilder;
        Extension::$routers = $router;
        $result = Extension::path('undocumented');

        $this->assertSame('', $result);
    }

    /**
     * @covers \phpDocumentor\Plugin\Core\Xslt\Extension::path
     */
    public function testPathWithDocumentedElement()
    {
        $elementList = m::mock('phpDocumentor\Descriptor\Collection');
        $element = m::mock('phpDocumentor\Descriptor\Collection');
        $element->shouldReceive('offsetExists')->andReturn(true);
        $element->shouldReceive('offsetGet');
        $elementList->shouldReceive('get')->andReturn($element);

        $projectDescriptorBuilder = $this->givenAProjectDescriptorBuilder($elementList);

        $rule = m::mock('phpDocumentor\Transformer\Router');
        $rule->shouldReceive('generate')->andReturn('/classes/my.namespace.class.html');

        $router = $this->givenARouter($rule);

        Extension::$descriptorBuilder = $projectDescriptorBuilder;
        Extension::$routers = $router;
        $result = Extension::path('\\my\\namespace\\class');

        $this->assertSame('classes/my.namespace.class.html', $result);
    }

    /**
     * @covers \phpDocumentor\Plugin\Core\Xslt\Extension::typeOfElement
     */
    public function testTypeOfElementWithUrl()
    {
        $elementList = m::mock('phpDocumentor\Descriptor\Collection');
        $elementList->shouldReceive('get')->andReturn(null);

        $projectDescriptorBuilder = $this->givenAProjectDescriptorBuilder($elementList);

        $router = $this->givenARouter(null);

        Extension::$descriptorBuilder = $projectDescriptorBuilder;
        Extension::$routers = $router;
        $result = Extension::typeOfElement('http://phpdoc.org');

        $this->assertSame('url', $result);
    }

    /**
     * @covers \phpDocumentor\Plugin\Core\Xslt\Extension::typeOfElement
     */
    public function testTypeOfElementWithUndocumentedElement()
    {
        $elementList = m::mock('phpDocumentor\Descriptor\Collection');
        $elementList->shouldReceive('get')->andReturn(null);

        $projectDescriptorBuilder = $this->givenAProjectDescriptorBuilder($elementList);

        $router = $this->givenARouter(null);

        Extension::$descriptorBuilder = $projectDescriptorBuilder;
        Extension::$routers = $router;
        $result = Extension::typeOfElement('undocumented element');

        $this->assertSame('undocumented', $result);
    }

    /**
     * @covers \phpDocumentor\Plugin\Core\Xslt\Extension::typeOfElement
     */
    public function testTypeOfElementWithDocumentedElement()
    {
        $elementList = m::mock('phpDocumentor\Descriptor\Collection');
        $element = m::mock('phpDocumentor\Descriptor\Collection');
        $element->shouldReceive('offsetExists')->with('my\\namespace')->andReturn(false);
        $element->shouldReceive('offsetExists')->with('~\\my\\namespace')->andReturn(true);
        $element->shouldReceive('offsetGet')->with('~\\my\\namespace')->andReturn('the namespace descriptor');
        $elementList->shouldReceive('get')->andReturn($element);

        $projectDescriptorBuilder = $this->givenAProjectDescriptorBuilder($elementList);

        $rule = m::mock('phpDocumentor\Transformer\Router');
        $rule->shouldReceive('generate')->andReturn('/classes/my.namespace.class.html');

        $router = $this->givenARouter(null);

        Extension::$descriptorBuilder = $projectDescriptorBuilder;
        Extension::$routers = $router;
        $result = Extension::typeOfElement('my\\namespace');

        $this->assertSame('documented', $result);
    }

    private function givenAProjectDescriptorBuilder($elementList)
    {
        $projectDescriptor = m::mock('\phpDocumentor\Descriptor\ProjectDescriptor');
        $projectDescriptor->shouldReceive('getIndexes')->andReturn($elementList);
        $projectDescriptorBuilder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $projectDescriptorBuilder->shouldReceive('getProjectDescriptor')->andReturn($projectDescriptor);
        return $projectDescriptorBuilder;
    }

    private function givenARouter($rule)
    {
        $queue = m::mock('phpDocumentor\Transformer\Router\Queue');
        $router = m::mock('phpDocumentor\Transformer\Router\StandardRouter');
        $queue->shouldReceive('insert');
        $router->shouldReceive('match')->andReturn($rule);
        return $router;
    }
}
