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

namespace phpDocumentor\Infrastructure\Renderer\Template;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Renderer\Template;
use phpDocumentor\DomainModel\Renderer\Template\Parameter;
use phpDocumentor\Infrastructure\Renderer\Template\LocalPathsRepository;

/**
 * Tests the functionality for the PathsRepository class.
 * @coversDefaultClass phpDocumentor\Infrastructure\Renderer\Template\LocalPathsRepository
 */
class LocalPathsRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * Directory structure
     *
     * @var array
     */
    private $filesystem = [
        'dummy' => [
            'data' => [
                'templates' => [
                    'clean' => ['template.xml' => ''],
                    'abstract' => ['template.xml' => '']
                ],
            ]
        ],
    ];

    /**
     * @var LocalPathsRepository
     */
    private $fixture;

    protected function setUp()
    {
        $this->root = vfsStream::setup('root', null, $this->filesystem);

        $this->fixture = new LocalPathsRepository(['vfs://root','vfs://root/dummy/data/templates']);
    }

    /**
     * @covers ::__construct
     * @covers ::listLocations
     */
    public function testListLocationsWhenDirectoryExists()
    {
        $parameter = new Parameter('directory', './data');
        $template = new Template('clean');
        $template->with($parameter);

        $this->assertEquals(
            ['./data','vfs://root', 'vfs://root/dummy/data/templates'],
            $this->fixture->listLocations($template)
        );
    }

    /**
     * @covers ::__construct
     * @covers ::listLocations
     */
    public function testListLocationsWhenDirectoryDoesNotExist()
    {
        $parameter = new Parameter('directory', './xxx');
        $template = new Template('clean');
        $template->with($parameter);

        $this->assertEquals(
            ['vfs://root/dummy/data/templates/clean', 'vfs://root', 'vfs://root/dummy/data/templates'],
            $this->fixture->listLocations($template)
        );
    }

    /**
     * @covers ::findByTemplateAndPath
     */
    public function testFindATemplate()
    {
        $template = new Template('clean');
        $path = new Path('template.xml');

        $this->assertEquals(
            'vfs://root/dummy/data/templates/clean/template.xml',
            $this->fixture->findByTemplateAndPath($template, $path)
        );
    }

    /**
     * @covers ::findByTemplateAndPath
     */
    public function testReturnNullIfATemplateIsNotFound()
    {
        $template = new Template('xxx');
        $path = new Path('template.xml');

        $this->assertEquals(
            null,
            $this->fixture->findByTemplateAndPath($template, $path)
        );
    }

    /**
     * @covers ::findByTemplateAndPath
     * @expectedException \RuntimeException
     */
    public function testThrowAnExceptionIfTemplateIsNotReadable()
    {
        vfsStream::newFile('xxx', 0111)->at($this->root);
        $template = new Template('clean');
        $path = new Path('xxx');

        $this->fixture->findByTemplateAndPath($template, $path);
    }

    /**
     * @covers ::listTemplates
     */
    public function testListAllTemplates()
    {
        $templates = $this->fixture->listTemplates();

        $this->assertEquals(['clean', 'abstract'], $templates);
    }
}
