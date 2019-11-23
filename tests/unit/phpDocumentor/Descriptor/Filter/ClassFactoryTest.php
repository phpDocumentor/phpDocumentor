<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use League\Pipeline\Pipeline;

/**
 * Tests the functionality for the ClassFactory class.
 */
class ClassFactoryTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ClassFactory $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new ClassFactory();
    }

    /**
     * @covers \phpDocumentor\Descriptor\Filter\ClassFactory::getChainFor
     */
    public function testGetChainForReturnsInstanceOfFilterChain() : void
    {
        $filterChain = $this->fixture->getChainFor('foo');

        $this->assertInstanceOf(Pipeline::class, $filterChain);
    }
}
