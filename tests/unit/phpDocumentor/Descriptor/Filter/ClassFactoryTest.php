<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

/**
 * Tests the functionality for the ClassFactory class.
 */
class ClassFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ClassFactory $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ClassFactory();
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\ClassFactory::getChainFor
     */
    public function testGetChainForReturnsInstanceOfFilterChain()
    {
        $filterChain = $this->fixture->getChainFor('foo');

        $this->assertInstanceOf('Zend\Filter\FilterChain', $filterChain);
    }
}
