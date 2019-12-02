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

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use const DIRECTORY_SEPARATOR;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Template\PathResolver
 */
final class PathResolverTest extends TestCase
{
    /** @var string */
    private $templatePath = 'vfs://root/templatePath';

    /** @var PathResolver */
    private $fixture;

    protected function setUp() : void
    {
        $this->fixture = new PathResolver($this->templatePath);
    }

    /**
     * @covers ::__construct
     */
    public function testIfDependencyIsCorrectlyRegisteredOnInitialization() : void
    {
        $this->assertSame($this->templatePath, $this->fixture->getTemplatePath());
    }

    /**
     * @covers ::resolve
     */
    public function testResolveWithInvalidAbsolutePath() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->givenAVirtualFileSystem([]);
        $this->fixture->resolve('vfs://root/myFolder/myTemplate');
    }

    /**
     * @covers ::resolve
     */
    public function testResolveWithInvalidName() : void
    {
        $this->expectException('InvalidArgumentException');
        $this->givenAVirtualFileSystem([]);
        $this->fixture->resolve('invalidName');
    }

    /**
     * @covers ::resolve
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
     * @covers ::resolve
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
     * @covers ::getTemplatePath
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
