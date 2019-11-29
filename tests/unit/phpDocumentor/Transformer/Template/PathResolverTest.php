<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace phpDocumentor\Transformer\Template;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;
use const DIRECTORY_SEPARATOR;

class PathResolverTest extends MockeryTestCase
{
    /** @var string */
    protected $templatePath = 'vfs://root/templatePath';

    /** @var PathResolver */
    protected $fixture;

    /**
     * Sets up the fixture with mocked dependency.
     */
    protected function setUp() : void
    {
        $this->fixture = new PathResolver($this->templatePath);
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\PathResolver::__construct
     */
    public function testIfDependencyIsCorrectlyRegisteredOnInitialization() : void
    {
        $this->assertSame($this->templatePath, $this->fixture->getTemplatePath());
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\PathResolver::resolve
     */
    public function testResolveWithInvalidAbsolutePath() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->givenAVirtualFileSystem([]);
        $this->fixture->resolve('vfs://root/myFolder/myTemplate');
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\PathResolver::resolve
     */
    public function testResolveWithInvalidName() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->givenAVirtualFileSystem([]);
        $this->fixture->resolve('invalidName');
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\PathResolver::resolve
     */
    public function testResolveWithValidAbsolutePath() : void
    {
        $this->givenAVirtualFileSystem(['template.xml' => 'xml']);
        $this->assertSame(
            vfsStream::url('root/templatePath') . DIRECTORY_SEPARATOR . 'myTemplate',
            $this->fixture->resolve('vfs://root/myFolder/myTemplate')
        );
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\PathResolver::resolve
     */
    public function testResolveWithValidName() : void
    {
        $this->givenAVirtualFileSystem([]);
        $this->assertSame(
            vfsStream::url('root/templatePath') . DIRECTORY_SEPARATOR . 'Clean',
            $this->fixture->resolve('Clean')
        );
    }

    /**
     * @covers \phpDocumentor\Transformer\Template\PathResolver::getTemplatePath
     */
    public function testGetTemplatePath() : void
    {
        $this->assertSame($this->templatePath, $this->fixture->getTemplatePath());
    }

    /**
     * Creates a virtual file system with a folder for $templatePath and
     * an folder as a location for a non-default template.
     * $template will either be an array that creates the template.xml in de virtual
     * file system structure in case of a test with a valid absolute path,
     * or an empty array
     */
    private function givenAVirtualFileSystem($template) : void
    {
        $structure = [
            'templatePath' => [
                'Clean' => ['template.xml' => 'xml'],
            ],
            'myFolder' => ['myTemplate' => $template],
        ];
        vfsStream::setup('root');
        vfsStream::create($structure);
    }
}
