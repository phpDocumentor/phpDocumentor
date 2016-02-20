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

namespace phpDocumentor\DomainModel\Renderer\Asset;

use phpDocumentor\DomainModel\Path;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Renderer\Asset\Folder
 * @covers ::<private>
 */
final class FolderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @coversNothing
     */
    public function itShouldBeACollection()
    {
        $folder = new Folder(new Path('images'), []);
        $this->assertInstanceOf(\ArrayObject::class, $folder);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::path
     */
    public function itShouldExposeThePathForThisFolder()
    {
        $path = new Path('images');

        $folder = new Folder($path, []);

        $this->assertSame($path, $folder->path());
    }

    /**
     * @test
     * @covers ::offsetSet
     */
    public function itCanAddAdditionalPathsInThisFolder()
    {
        $folder = new Folder(new Path('images'), []);
        $path = new Path('cats.png');

        $folder[] = $path;

        $this->assertSame($path, $folder[0]);
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function itShouldErrorWhenTheConstructorReceivesSomethingOtherThanAPathObject()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        new Folder(new Path('images'), [new \stdClass()]);
    }

    /**
     * @test
     * @covers ::offsetSet
     */
    public function itShouldErrorWhenTheSomethingOtherThanAPathObjectIsAdded()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $folder = new Folder(new Path('images'), []);

        $folder[] = new \stdClass();
    }
}
