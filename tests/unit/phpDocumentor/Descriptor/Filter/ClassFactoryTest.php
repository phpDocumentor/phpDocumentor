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

namespace phpDocumentor\Descriptor\Filter;

use League\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the ClassFactory class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\Filter\ClassFactory
 */
final class ClassFactoryTest extends TestCase
{
    /** @var ClassFactory $fixture */
    private $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new ClassFactory();
    }

    /**
     * @covers ::getChainFor
     */
    public function testGetChainForReturnsInstanceOfFilterChain() : void
    {
        $filterChain = $this->fixture->getChainFor('foo');

        $this->assertInstanceOf(Pipeline::class, $filterChain);
    }
}
