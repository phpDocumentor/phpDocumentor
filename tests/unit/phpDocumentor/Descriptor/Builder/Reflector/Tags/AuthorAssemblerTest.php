<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2019 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 *
 *
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Tag\AuthorDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Author;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Descriptor\Builder\Reflector\Tags\AuthorAssembler
 */
class AuthorAssemblerTest extends TestCase
{
    public function testCreate() : void
    {
        $feature = new AuthorAssembler();
        $result = $feature->create(new Author('Jaapio', 'jaap@phpdoc.org'));

        self::assertInstanceOf(AuthorDescriptor::class, $result);
        self::assertEquals('Jaapio <jaap@phpdoc.org>', $result->getDescription());
    }
}
