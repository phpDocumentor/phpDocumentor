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

namespace phpDocumentor\Compiler;

use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Tests the functionality for the Compiler.
 */
class CompilerTest extends MockeryTestCase
{
    /** @var Compiler $fixture */
    protected $fixture;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp() : void
    {
        $this->fixture = new Compiler();
    }

    /**
     * @covers \phpDocumentor\Compiler\Compiler::insert
     */
    public function testDefaultPassHasDefaultPriority() : void
    {
        $this->fixture->insert($this->prophesize(CompilerPassInterface::class)->reveal());
        $this->fixture->setExtractFlags(Compiler::EXTR_PRIORITY);

        $this->assertEquals(Compiler::PRIORITY_DEFAULT, $this->fixture->extract());
    }
}
