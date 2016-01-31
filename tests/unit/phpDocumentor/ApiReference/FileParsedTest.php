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

namespace phpDocumentor\ApiReference;

use phpDocumentor\DomainModel\Documentation\Api\FileParsed;

/**
 * @coversDefaultClass phpDocumentor\ApiReference\FileParsed
 * @covers ::<private>
 */
final class FileParsedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::filename
     */
    public function testFilenameIsPassedToEvent()
    {
        $filename = 'my_file';

        $event = new FileParsed($filename);

        $this->assertSame($filename, $event->filename());
    }
}
