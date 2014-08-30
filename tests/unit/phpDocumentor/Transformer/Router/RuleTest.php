<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Router\Rule::__construct
     * @covers phpDocumentor\Transformer\Router\Rule::match
     */
    public function testIfRuleCanBeMatched()
    {
        $fixture = new Rule(
            function () {
                return true;
            },
            function () {
            }
        );
        $fixture2 = new Rule(
            function () {
                return false;
            },
            function () {
            }
        );

        $node = 'test';
        $this->assertTrue($fixture->match($node));
        $this->assertFalse($fixture2->match($node));
    }

    /**
     * @covers phpDocumentor\Transformer\Router\Rule::__construct
     * @covers phpDocumentor\Transformer\Router\Rule::generate
     */
    public function testIfUrlCanBeGenerated()
    {
        $fixture = new Rule(
            function () {
            },
            function () {
                return 'url';
            }
        );

        $this->assertSame('url', $fixture->generate('test'));
    }

    /**
     * @covers phpDocumentor\Transformer\Router\Rule::__construct
     * @covers phpDocumentor\Transformer\Router\Rule::generate
     * @covers phpDocumentor\Transformer\Router\Rule::translateToUrlEncodedPath
     */
    public function testTranslateToUrlEncodedPath()
    {
        $fixture = new Rule(
            function () {
                return true;
            },
            function () {
                return 'httö://www.€xample.org/foo.html#bär';
            }
        );

        $this->assertSame('httö://www.EURxample.org/foo.html#bär', $fixture->generate('test'));
    }
}
