<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
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
